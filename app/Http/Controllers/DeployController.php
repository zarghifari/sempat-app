<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller
{
    public function run(Request $request)
    {
        if ($request->query('token') !== config('app.deploy_token')) {
            return response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $steps = [
            ['migrate',        ['--force' => true], 'Database Migration'],
            ['config:clear',   [],                   'Clear Config Cache'],
            ['cache:clear',    [],                   'Clear Application Cache'],
            ['route:clear',    [],                   'Clear Route Cache'],
            ['view:clear',     [],                   'Clear View Cache'],
            ['storage:link',   [],                   'Create Storage Link'],
            ['config:cache',   [],                   'Cache Configuration'],
            ['route:cache',    [],                   'Cache Routes'],
            ['view:cache',     [],                   'Cache Views'],
        ];

        $log = [];
        foreach ($steps as [$cmd, $opts, $label]) {
            try {
                $code = Artisan::call($cmd, $opts);
                $out  = Artisan::output();
                $log[] = [
                    'step'   => $label,
                    'cmd'    => trim($cmd.' '.implode(' ', array_map(fn($k,$v)=>$v===true?$k:"$k=$v", array_keys($opts), $opts))),
                    'code'   => $code,
                    'output' => trim($out),
                    'ok'     => $code === 0,
                ];
            } catch (\Throwable $e) {
                $log[] = [
                    'step'   => $label,
                    'cmd'    => $cmd,
                    'code'   => 1,
                    'output' => 'Exception: '.$e->getMessage(),
                    'ok'     => false,
                ];
            }
        }

        return response()->json([
            'app'   => config('app.name'),
            'env'   => app()->environment(),
            'php'   => PHP_VERSION,
            'time'  => now()->toDateTimeString(),
            'log'   => $log,
        ]);
    }
}