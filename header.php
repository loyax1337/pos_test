<html>
    <head>
        <title>EBS | Portal</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <link href="https://fonts.cdnfonts.com/css/lucida-handwriting-std" rel="stylesheet">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="shortcut icon" href="../images/favicon.ico">

        <style>
            body {
                background: #161618;
                background-image: linear-gradient(to bottom, #DCDDD5 30%, #212124 80%, #161618 100%);
                background-size: cover;
                background-repeat: no-repeat;
                color: #182C3B;
                font-family: Arial;
                user-select: none;
                margin: 0;
            }
            hr{
                border: none;
                height: 2px;
                background-color: #2E7DA1;
                width: 100%;         /* Center-aligned by default */
            }
            /* width */
            ::-webkit-scrollbar {
              width: 5px;
              height: 5px;
            }
            /* Track */
            ::-webkit-scrollbar-track {
              background: #f1f1f1; 
            }
             
            /* Handle */
            ::-webkit-scrollbar-thumb {
              background: #DBC687; 
            }

            /* Handle on hover */
            ::-webkit-scrollbar-thumb:hover {
              background: #DBC687; 
            }

            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
            }

            /*if screen sizes into big*/
            @media (min-width: 700px) {
                body {
                   display: flex;
                   justify-content: center;
                }
                div.main-container {
                    width: 50%;
                }
                div.itemlst-partition-col {
                    display: flex;
                    flex-direction: row;
                }
                div.content-ctrl-fltr-wrap {
                    display: flex;
                    flex-direction: row;
                    justify-content: center;
                }
            }

            div.main-container {
                height: 100%;
                transition: width 1s;
            }

            div.content-cover {
                height: 250px;
                display: flex;
                flex-direction: row;
                justify-content: center;
            }

            div.content-cover div.cover-bio, div.content-cover div.cover-img {
                padding-top: 20px;
                height: 250px;
                width: 50%;
            }

            div.cover-bio div {
                padding: 10px;
            }
            div.cover-bio div.cover-bio-compname {
                //font-family: 'Times New Roman', Times, serif;
                font-size: 3vh;
                //color: #EB6A07;

            }
            div.cover-bio div.cover-bio-compvission {
                font-family: 'Lucida Handwriting Std', sans-serif;
                font-size: 12px;
            }

            div.cover-img {
                text-align: right;
            }
            div.cover-img img {
                width: 200px;
                height: 200px;
                object-fit: cover;
            }

            div.content-login-wrap {
                padding: 10px;
            }
            div.content-title {
                padding: 10px;
            }
            div.content-txt-textbox {
                width: 100%;
                padding: 10px;
            }
            div.content-txt-textbox input[type='text'], input[type='password'], input[type='tel'] {
                padding: 10px;
                width: 100%;
                height: 35px;
                max-width: 300px;
                border: 1px solid #9B9C68;
                border-radius: 3px;
                font-size: 12px;
                outline-color: #946C52;
            }
            div.content-btn-loginbtn {
                width: 100%;
                padding: 10px;
            }
            div.content-btn-loginbtn input[type='submit'] {
                width: 100%;
                height: 35px;
                max-width: 300px;
                border: 1px solid #9B9C68;
                border-radius: 3px;
                background-image: linear-gradient(#182C3B, #4A5B63);
                color: #ededed;
                font-size: 12px;
                transition: background-color 0.3s;
            }
            div.content-btn-loginbtn input[type='submit']:hover {
                background-color: #182C3B;
                background-image: none;
            }

            div.content-link {
                padding: 10px;
                color: #ededed;
                font-size: 12px;
            }

            div.content-rdo {
                padding: 10px;
                font-size: 15px;
            }

            div.content-txt-date {
                padding: 10px;
                font-size: 12px;
            }
            div.content-txt-date input[type='date'] {
                height: 30px;
                border: 1px solid #9B9C68;
                border-radius: 3px;
                padding: 10px;

            }
    </style>
    </style>
  </head>
  <body>