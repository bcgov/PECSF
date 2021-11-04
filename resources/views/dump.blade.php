<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div>
        samaccountname: <span id="samaccountname"></span>
    </div>
<script>
    function parseJwt (token) {
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace('-', '+').replace('_', '/');
        return JSON.parse(window.atob(base64));
    };

    const samaccountname = parseJwt("{{$data['id_token']}}")['samaccountname'];
    document.querySelector("#samaccountname").innerHTML = samaccountname;
</script>

</body>
</html>
@php
    dd($data);
@endphp
