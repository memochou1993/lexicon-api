<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lexicon Demo</title>
        <link rel="icon" href="icon.png">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="demo">
                Lexicon Demo
            </a>
            @if($language == 'en')
            <button onclick="javascript:location.href='?language=tw'" class="btn btn-sm btn-outline-light">
                TW
            </button>
            @endif
            @if($language == 'tw')
            <button onclick="javascript:location.href='?language=en'" class="btn btn-sm btn-outline-light">
                EN
            </button>
            @endif
        </nav>
        <div class="container mb-5">
            <div class="mt-4">
                <div class="card">
                    <div class="card-body">
                        <span class="mr-2">
                            <button onclick="javascript:location.href='?language={{ $language  }}&sync=true'" class="btn btn-sm btn-info my-1 my-md-0">
                                Sync Language Files
                            </button>
                        </span>
                        <span class="mr-2">
                            <button onclick="javascript:location.href='?language={{ $language  }}&clear=true'" class="btn btn-sm btn-danger my-1 my-md-0">
                                Clear Language Files
                            </button>
                        </span>
                        @if(count($keys))
                        <span class="mr-2">
                            <button onclick="javascript:window.open('?language={{ $language  }}&dump=true')" class="btn btn-sm btn-secondary my-1 my-md-0">
                                Dump Language File
                            </button>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="my-4">
                @if(count($keys))
                <table class="table table-responsive-sm table-bordered table-hover table-striped">
                    <thead>
                    <tr class="text-center">
                        <th>PHP Code in Blade Template</th>
                        <th>PHP Code in Language File</th>
                        <th>Translation</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($keys as $key => $value)
                        <tr>
                            <td>
                                ___('{{ $key }}')
                            </td>
                            <td>
                                '{{ $key }}' => '{{ $value }}',
                            </td>
                            <td>
                                {{ ___($key) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </body>
</html>
