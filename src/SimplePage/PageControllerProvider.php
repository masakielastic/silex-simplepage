<?php
namespace SimplePage;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class PageControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/pages', function(Application $app, Request $request) {

          $name = $request->query->get('name');

          if ($name !== null) {
            $statement = $app['db']->executeQuery('SELECT * FROM page WHERE name = ?', [$name]);
            $page = $statement->fetch();
          } else {
            $page = $app['db']->fetchAll('SELECT * FROM page LIMIT 10');
          }

          $status = 200;

          if (!$page) {

            $page = [
              'id' => null,
              'name' => $name,
              'title' => 'エラー',
              'body' => 'ページが存在しません。'
            ];

            $status = 404;
          }

          return $app->json($page, $status);
        });

        $controllers->match('/pages', function(Application $app, Request $request) {
            $data = json_decode($request->getContent(), true);
            $name = $data['name'];
            $title = $data['title'];
            $body = $data['body'];

            if ($name === null || $title === null || $body === null) {
                return $app->json(['msg' => 'fail'], 400);
            }

            $app['db']->executeUpdate('INSERT INTO page(name, title, body) values(?, ?, ?)', [$name, $title, $body]);

            return $app->json(['msg' => 'ok']);

        })->method('POST');

        $controllers->match('/pages/{id}', function(Application $app, Request $request, $id) {
            $data = json_decode($request->getContent(), true);

            $title = $data['title'];
            $body = $data['body'];

            if ($id === null || $title === null || $body === null) {
                return $app->json(['msg' => 'fail'], 400);
            }

            $count = $app['db']->executeUpdate('UPDATE page SET title = ?, body = ? WHERE id = ?', [$title, $body, $id]);

            return $app->json(['msg' => 'ok']);
        })->method('PUT|PATCH');

        $controllers->delete('/api/pages/{id}', function(Application $app, Request $request, $id) {
            $count = $app['db']->executeUpdate('DELETE FROM page WHERE id = ?', [$id]);

            return $app->json(['msg' => 'ok']);
        });

        return $controllers;
    }
}
