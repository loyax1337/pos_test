<html>
    <head>
        <title>EBS | Home</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <link href="https://fonts.cdnfonts.com/css/lucida-handwriting-std" rel="stylesheet">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body {
                background: #DCDDD5;
                background-image: linear-gradient(to bottom, #161618 30%, #212124 80%, #DCDDD5 100%);
                background-size: cover;
                background-repeat: no-repeat;
                color: #182C3B;
                font-family: Arial;
                user-select: none;
                margin: 0;
                align-items: center;
                display: flex;
                justify-content: center;
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
                div.main-container {
                    //width: 50%;
                }
            }

            div.main-container {
                //background-image: linear-gradient(to bottom, #FFD57E 50%, #EB6A07 100%);
                height: 50%;
                width: 100%;
                display: flex;
                //flex-direction: column;
                justify-content: center;
                //transition: width 1s;
                //min-width: 620px;
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
                //color: #ffffff;
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

            div.btnhome-redir-portal {
                padding: 10px;
            }
            div.btnhome-redir-portal button {
                width: 100px;
                height: 35px;
                background-image: linear-gradient(270deg, #000000, #22262A);
                color: #ededed;
                border: 0.2px solid #9B9C68;
                border-bottom-left-radius: 10px;
                border-top-right-radius: 10px;
                font-size: 12px;
                transition: background-color, border-radius 0.3s;
            }
            div.btnhome-redir-portal button:hover {
                background-image: none;
                background-color: #22262A;
                border-radius: 1px;
            }
    </style>
  </head>
  <body>
    <div class="main-container">
        <div style="border-radius: 5px;padding: 10px;background-image: linear-gradient(to bottom, #DCDDD5 70%, #212124 100%);">
            <div class="content-cover">
                <div class="cover-bio">
                    <div class="cover-bio-compname"><span class="w3-center w3-animate-right">Point of Sale (POS)</span></div>
                    <div class="cover-bio-compvission"><span class="w3-center w3-animate-right"></span></div>
                </div>
                <div class="cover-img"><img src="images/syslogo.png" class="w3-container w3-center w3-animate-top" /></div>
            </div>
            <div class="btnhome-redir-portal"><button id="btn-redir-portal" type="button">Log In</button></div>
        </div>
    </div>
    <script>
        function goToPortal() {
            this.document.location.href = 'login.php';
        }
        $( document ).ready(function() {
            $("#btn-redir-portal").click(function() {
                goToPortal();
            });
        });
    </script>
  </body>
  </html>