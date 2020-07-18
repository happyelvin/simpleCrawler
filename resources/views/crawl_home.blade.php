<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            /*.flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }*/

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .center {
            	text-align: center;
            }

            ul {
            	display: inline-block;
            }

            a {
            	color: #636b6f;
			    text-decoration: none;
			}

			a:hover 
			{
			     color:#00A0C6; 
			     text-decoration:none; 
			     cursor:pointer;  
			}
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md" style="padding-top: 150px">
                    Crawler
                </div>

            </div>
            <div class="center">
	            <h4>List of API Available</h4>
	            <ul style="margin-top: -10px">
	                <li><a href="{{$url}}/api/all" target="_blank">{{$url}}/api/all</a></li>
	                <li><a href="{{$url}}/api/page/5" target="_blank">{{$url}}/api/page/5</a></li>
	            </ul>

	            <div style="margin-top: 50px">
	            	It's recommended to visit the api with tools like Postman or Insomnia.
	            </div>
        	</div>
        </div>
    </body>
</html>
