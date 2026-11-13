<html>
    <head>
        <title>EBS | POS Payment</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <!--<script src="ajax/libs/jquery/3.7.1/jquery.min.js"></script>-->
        <link href="https://fonts.cdnfonts.com/css/lucida-handwriting-std" rel="stylesheet">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body {
                background: #EB6A07;
                background-image: linear-gradient(to bottom, #DCDDD5 25%, #FFD57E 60%, #EB6A07 100%);
                background-size: cover;
                background-repeat: no-repeat;
                color: #182C3B;
                font-family: Arial;
                user-select: none;
                margin: 0;
                
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
                //min-width: 620px;
            }

            div.content-cover {
                height: 200px;
                display: flex;
                flex-direction: row;
                justify-content: center;
                background-color: #ffffff;
                border-bottom: 2px solid #CECECE;
                border-bottom-left-radius: 5px;
                border-bottom-right-radius: 5px;
            }

            div.content-cover div.cover-bio, div.content-cover div.cover-img {
                padding-top: 20px;
                height: 200px;
                width: 50%;
            }

            div.cover-bio div {
                padding: 10px;
            }
            div.cover-bio div.cover-bio-title {
                font-size: 3vh;
                color: #0a0a0a;

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
                height: 100px;
                //object-fit: cover;
            }

            div.content-payment {
                padding: 10px;
                background-color: #ffffff;
            }

            div.content-lbl {
                padding: 5px;
            }
            div.content-lbl label {
               min-width:300px;
               height: 35px;
               font-size: 15px;
               font-weight: bold;
               color: gray;
            }
            div.content-txt {
                padding: 5px;
            }
            div.content-txt input {
               padding: 10px;
               min-width:300px;
               height: 35px;
               border: 0.5px solid gray;
               border-radius: 3px;
               font-size: 12px;
               color: #22262A;
               outline-color: #9B9C68;
            }
            div.content-cbo {
                padding: 5px;
            }
            div.content-cbo select {
                padding: 10px;
                width: 100px;
                height: 35px;
                border: 0.5px solid gray;
                border-radius: 3px;
                font-size: 10px;
                color: #0a0a0a;
                outline-color: #9B9C68;
            }
    </style>
  </head>
  <body>
    <div class="main-container">
        <div class="content-cover">
            <div class="cover-bio">
                <div class="cover-bio-title"><span class="w3-center w3-animate-right">Payment Entry</span></div>
                <div class="cover-bio-compvission"><span class="w3-center w3-animate-right"></span></div>
            </div>
            <div class="cover-img"><img src="images/syspayment-logo.png" class="w3-container w3-center w3-animate-top" /></div>
        </div>
        <div class="content-payment"></div>
    </div>
    <script>
        function ajaxLoadHTMLToElement(pcUrl,pcElement) {
            $.ajax({
                type: "POST",
                url: pcUrl,
                success: function(data) {
                    $("."+pcElement).html(data);
                }
            });
        }

        $(".content-payment").ready(function() {
            ajaxLoadHTMLToElement("paymentlst.php","content-payment");
        });        
    </script>
  </body>
  </html>