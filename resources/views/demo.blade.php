<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ ___('project.name') }}</title>
        <link rel="icon" href="icon.png">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="demo">
                {{ ___('project.name') }}
            </a>
            <div>
                @if($language == 'en')
                <button onclick="javascript:location.href='?language=zh'" class="btn btn-sm btn-outline-light">
                    ZH
                </button>
                <button onclick="javascript:location.href='?language=en'" class="btn btn-sm bg-light">
                    EN
                </button>
                @endif
                @if($language == 'zh')
                <button onclick="javascript:location.href='?language=zh'" class="btn btn-sm bg-light">
                    ZH
                </button>
                <button onclick="javascript:location.href='?language=en'" class="btn btn-sm btn-outline-light">
                    EN
                </button>
                @endif
            </div>
        </nav>
        <div class="container mb-5">
            <div class="mt-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <span class="mr-2">
                            <button onclick="javascript:location.href='?language={{ $language  }}&sync=true'" class="btn btn-sm btn-info my-1 my-md-0" id="sync">
                                {{ ___('action.sync') }}
                            </button>
                        </span>
                        <span class="mr-2">
                            <button onclick="javascript:location.href='?language={{ $language  }}&clear=true'" class="btn btn-sm btn-danger my-1 my-md-0" id="clear">
                                {{ ___('action.clear') }}
                            </button>
                        </span>
                        @if(count($keys))
                        <span class="mr-2">
                            <button onclick="javascript:window.open('?language={{ $language  }}&dump=true')" class="btn btn-sm btn-secondary my-1 my-md-0">
                                {{ ___('action.dump') }}
                            </button>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="my-4" id="table">
                @if(count($keys))
                <table class="table table-bordered table-responsive-sm bg-light">
                    <thead>
                    <tr class="text-center">
                        <th>{{ ___('table.header.code_in_blade_template') }}</th>
                        <th>{{ ___('table.header.translation') }}</th>
                        <th>{{ ___('table.header.code_in_language_file') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($keys as $key => $value)
                            @if($language == 'en')
                            <tr>
                                <td>
                                    ___('{{ $key }}')
                                </td>
                                <td>
                                    {{ ___($key) }}
                                </td>
                                <td rowspan="2">
                                    '{{ $key }}' => '{{ $value }}',
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    ___('{{ $key }}', 2)
                                </td>
                                <td>
                                    {{ ___($key, 2) }}
                                </td>
                            </tr>
                            @endif
                            @if($language == 'zh')
                                <tr>
                                    <td>
                                        ___('{{ $key }}')
                                    </td>
                                    <td>
                                        {{ ___($key) }}
                                    </td>
                                    <td>
                                        '{{ $key }}' => '{{ $value }}',
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            <div class="my-5 text-center" id="loading" hidden>
                <h5 class="py-3" id="message"></h5>
                <div style="width: 4rem; height: 4rem;" class="spinner-grow text-warning" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </body>
</html>

<script>
document.getElementById('sync').addEventListener('click', () => {
    document.getElementById('table').hidden = true;
    document.getElementById('loading').hidden = false;
    document.getElementById('message').innerHTML = 'Fetching resources...';
    setTimeout(() => {
        document.getElementById('message').innerHTML = 'Generating language files...';
    }, 1000);
});

document.getElementById('clear').addEventListener('click', () => {
    document.getElementById('table').hidden = true;
    document.getElementById('loading').hidden = false;
    document.getElementById('message').innerHTML = 'Deleting language files...';
});
</script>

<style>
body {
    font-family: 'Microsoft Jhenghei';
    font-size: 0.75rem;
}

table {
    table-layout: fixed;
}
</style>
