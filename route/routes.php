<?php
/*
* -----------------------------
* Define all API routes in one location
* -----------------------------
*/

$app->with('/api', function () use ($app) {
    $app->with('/menu', function () use ($app) {
        // api/menu/:date => Return all meal list for the given date
        $app->respond(['GET', 'POST'], '/[:date]', function ($request, $response) {
            $statusCode = 200; // default status code for success
            $responseData = array(); // empty data
            try {
                $date = $request->date;
                $data = new DataLayer\Data();
                $responseData = $data->get($date);
            } catch (Exception $ex) {
                $statusCode = 400;
                $responseData = ['error' => $ex->getMessage()];
            } finally {
                return $response->code($statusCode)->json($responseData);
            }
        });
        // api/menu => Return all meal list for this month
        $app->respond(['GET', 'POST'], '/?', function ($request, $response) {
            $statusCode = 200;
            $data = new DataLayer\Data();
            $responseData = $data->getAll();
            return $response->code($statusCode)->json($responseData);
        });
    });
});
