<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unprocessable Entity</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            padding: 0;
            height: 100vh;
            width: 100%;
            background: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        body::before {
            position: absolute;
            z-index: -1;
            display: block;
            content: attr(data-code);
            color: #F5F5F5;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: clamp(12rem, 40vw, 35rem);
            text-align: center;
            letter-spacing: -10px;
            opacity: .8;
        }

        h1 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            margin: 0 0 15px 0;
            color: rgba(38,50,56 ,1);
            max-width: 90%;
        }
        p {
            font-family: 'Inter', sans-serif;
            font-weight: 300;
            font-size: .9rem;
            text-align: center;
            margin: 0 0 25px 0;
            color: rgba(55,71,79 ,1);
            max-width: 90%;
        }
        a {
            background: rgba(98,0,234 ,1);
            color: white;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: .7rem;
            text-transform: uppercase;
            text-decoration: none;
            letter-spacing: 1px;
            padding: 15px 25px;
            border-radius: 7px;
        }
    </style>
</head>
<body data-code="422">
    <h1>Unprocessable Entity</h1>
    <p>{{ $exception->getMessage() }}</p>
    <a href="{{ url('') }}">Back To Homepage</a>
</body>
</html>