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
        button {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div>
        <ul>
            @if(count($images))
                <li>
                    <a href="/filepond/edit/{{ $id }}">
                        <button type="button ">&nbsp; EDIT LIST &nbsp;</button>
                    </a>
                </li>
                @foreach($images as $image)
                <li><img src="{{ $image->url }}" title="{{ $image->title }}"></li>
                @endforeach
                <li>
                    <a href="/filepond/edit/{{ $id }}">
                        <button type="button ">&nbsp; EDIT LIST &nbsp;</button>
                    </a>
                </li>
            @else
                <li>Empty...</li>
                <li>
                    <a href="/filepond">
                        <button type="button ">&nbsp; ADD IMAGES &nbsp;</button>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</body>
</html>