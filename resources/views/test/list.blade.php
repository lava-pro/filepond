<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Test | List</title>
    <style>
        li {
            list-style: none;
            display: block;
        }
    </style>
</head>
<body>
    <div>
        <ul>
            @if(count($images))
                @foreach($images as $image)
                <li><img src="{{ $image->url }}" title="{{ $image->title }}"></li>
                @endforeach
            @else
                <li>Empty...</li>
            @endif
        </ul>
    </div>
</body>
</html>