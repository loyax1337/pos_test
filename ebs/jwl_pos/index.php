<html>
    <head>
        <title>K&L Collections | POS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <!--<script src="ajax/libs/jquery/3.7.1/jquery.min.js"></script>-->
        <link href="https://fonts.cdnfonts.com/css/lucida-handwriting-std" rel="stylesheet">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                font-size: 3vh;

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


            div.content-items {
                padding: 10px;
            }

            div.content-itemlst-wrap {
                display: flex;
                flex-direction: column;
                padding: 2px;
            }

            div.itemlst-partition-col {
                justify-content: center;
            }
            div.itemlst-partition-row {
                display: flex;
                justify-content: center;
                flex-direction: row;
            }
            div.itemlst-box {
                width: 165px;
                height: 280px;
                padding: 5px;
                transition: padding 0.3s;
            }
            
            div.itemlst-box div.itemlst-box-content {
                border: 0.2px solid gray;
                border-radius: 5px;
                width: 160px;
                height: 270px;
                background-image: radial-gradient(#ffffff 1%,#DCDDD5 70%);
                
            }
            div.itemlst-box:hover div.itemlst-box div.itemlst-box-content{
                width: 165px;
                height: 245px;
            }
            div.itemlst-box:hover {
                padding-top: 0px;
            }

            div.itemlst-img img {
                width: 158px;
                height: 158px;
                object-fit: cover;
            }

            div.itemlst-name {
                padding: 5px;
                display: flex;
                justify-content: center;
            }
            div.itemlst-name span {
                font-size: 10px;
                color: #0a0a0a;
                text-align: center;
                height: 30px;
            }

            div.itemlst-prc {
                padding: 5px;
                display: flex;
                justify-content: center;
            }

            div.itemlst-prc span {
                font-size: 14px;
                color: #169612;
            }



            /*Contollers...*/
            div.btn-mode1 {
                padding: 2px;
                display: flex;
                justify-content: center;
            }
            div.btn-mode1 button {
                width: 100px;
                height: 35px;
                border: 0.2px solid #9B9C68;
                border-bottom-left-radius: 10px;
                border-top-right-radius: 10px;
                background-image: linear-gradient(270deg, #042DA3, #6AB1E0);
                color: #ededed;
                font-size: 10px;
                transition: background-color, border-radius 0.3s;
            }
            div.btn-mode1 button:hover {
                background-color: #042DA3;
                background-image: none;
                border-radius: 2px;
            }


            div.content-cart-wrap {
                width: 0px;
                height: 100%;
                background-color: #ededed;
                position: fixed;
                top: 0px;
                right: 0px;

                transition: width 1s;
                color: black;
                //z-index: 2;
            }

            div.cart-headings {
                display: flex;
                flex-direction: row;
                white-space: nowrap;
            }
            div.cart-headings div.cart-title{
                width: 100%;
                padding:10px;
            }
            div.cart-headings div.cart-title h3 {
                font-weight: bold;
                color: #22262A;
            }
            div.cart-headings div.cart-ctrl-exit {
                //width: 100%;
                vertical-align: baseline;
                padding:20px;
                font-size: 25px;
                display: flex;
                justify-content: right;
            }
            div.cart-headings div.cart-ctrl-exit button {
                border: none;
                width: 40px;
                color: gray;
                transition: transform 0.7s ease-in-out;
                background-color: transparent;
            }
            div.cart-headings div.cart-ctrl-exit button:hover {
                transform: rotate(180deg);
                color: #000000;
                background-color: #DCDCDC;
                border-radius: 50%;
            }

            div.content-ctrl-fltr-wrap {
                padding: 10px;
            }
            div.content-txt-search {
                padding: 5px;
            }
            div.content-txt-search input {
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
                color: #22262A;
                outline-color: #9B9C68;
            }

            div.content-grid {
                height: 360px;
                overflow-y: auto;
                //white-space: pre-wrap;
                padding: 0px;
            }
            div.content-grid table {
                font-family: arial, sans-serif;
                font-size: 12px;
                border-collapse: collapse;
                width: auto;
                color: black;
            }
            div.content-grid th {
                text-align: center;
                background-color: #35363a;
                color: white;
                border: 1px solid #dddddd;
            }
            div.content-grid td {
                text-align: left;
                border: none;
            }
            div.content-grid tr a {
                text-decoration: none;
            }
            div.content-grid tr a:hover {
                text-decoration: underline;
                text-decoration-color: #f87801;
            }
            div.content-grid td, th {
                padding: 12px;
            }
            div.content-grid tr.t_det:hover{
                background-color: #d6ae56;
                transition: 0.5s;
            }
            
            div.formWindow {
                background-image: radial-gradient(#ffffff 1%,#DCDDD5 70%);
                border-radius: 3px;
                transition: 0.2s;
                position:fixed;
                top: 50%;
                left: 50%;
                width: 0em;
                height: 0em;
                margin-top: 0em; /*set to a negative number 1/2 of your height*/
                margin-left: 0em; /*set to a negative number 1/2 of your width*/

            }

            div.cart-btn {
                padding: 5px;
                transition: padding-top 0.2s;
            }
            div.cart-btn img {
                height: 35px;
                width: 35px;
            }
            div.cart-btn:hover {
                padding-top: 1px;
            }
            div.exp-btn {
                padding: 5px;
                transition: padding-top 0.2s;
            }
            div.exp-btn img {
                height: 35px;
                width: 35px;
            }
            div.exp-btn:hover {
                padding-top: 1px;
            }
            div.cashreg-btn {
                padding: 5px;
                transition: padding-top 0.2s;
            }
            div.cashreg-btn img {
                height: 35px;
                width: 35px;
            }
            div.cashreg-btn:hover {
                padding-top: 1px;
            }
            div.logout-btn {
                padding: 8px;
                transition: padding-top 0.2s;
            }
            div.logout-btn img {
                height: 28px;
                width: 35px;
            }
            div.logout-btn:hover {
                padding-top: 1px;
            }


            div.cart-list {
                max-height: 600px;
                overflow-y: auto;
            }
            div.cart-list div.cart-list-box {
                padding: 10px;
            }

            div.cart-list div.cart-list-box div.cart-list-box-content {
                border: 1px solid #DCDCDC;
                border-radius: 2px;
                background-color: #ffffff;
                white-space: nowrap;
                overflow: hidden;
            }

            div.cart-list div.cart-list-box div.cart-list-box-content div.cart-list-box-content-header {
                display: flex;
                flex-direction: row;
                border-bottom: 1px solid #ededed;
            }
            div.cart-list div.cart-list-box div.cart-list-box-content div.cart-list-box-content-detail:hover {
                background-color: #add8e6;
                transition: background-color 0.3s;
            }
            div.cart-list div.cart-list-box div.cart-list-box-content div.cart-list-box-content-footer {
                height: 50px;
                padding: 10px;
                text-align: right;
                font-weight: bold;
                color: #45392E;
                background-color: #FFFCF1;
            }

            div.cart-list-box-content-header-img {
                padding: 10px;
            }
            div.cart-list-box-content-header-img img {
                height: 100px;
                width: 100px;
                object-fit: cover;
            }

            div.content-chkbox {
                padding: 10px;
                align-content: center;
            }
            div.content-chkbox input[type='checkbox'] {
                border: 1px solid gray;
                border-radius: 2px;
                transform: scale(1.5);
                accent-color: #169612;
            }

            div.cart-footer {
                display: flex;
                flex-direction: row;
            }
            div.cart-footer-total {
                white-space: nowrap;
                text-align: right;
                align-content: center;
                width: 240px;
                font-weight: bold;
                color: #169612;
                font-size: 12px;
            }

            div.btn-cart-empty {
                padding: 10px;
                text-align: right;
            }
            div.btn-cart-empty button {
                border: 1px solid #f56358;
                border-radius: 3px;
                height: 38px;
                width: 50px;
                background-color: #f56358;
                color: #ffffff;
                transition: background-color 0.3s;
            }
            div.btn-cart-empty button:hover {
                background-color: #C72519;
            }

            div.btn-cart-place-order {
                padding: 10px;
                text-align: right;
            }
            div.btn-cart-place-order button {
                border: 1px solid #0641EB;
                border-radius: 3px;
                font-size: 12px;
                height: 38px;
                width: 100px;
                background-color: #0641EB;
                color: #ffffff;
                transition: background-color 0.3s;
            }
            div.btn-cart-place-order button:hover {
                background-color: #042DA3;
            }
    </style>
  </head>
  <body>
    <div class="main-container">
        <div class="content-cover">
            <div class="cover-bio">
                <div class="cover-bio-compname"><span class="w3-center w3-animate-right">K&L COLLECTIONS | JEWELRIES CASHIER</span></div>
                <div class="cover-bio-compvission"><span class="w3-center w3-animate-right"></span></div>
            </div>
            <div class="cover-img"><img src="images/syslogo.png" class="w3-container w3-center w3-animate-top" /></div>
        </div>
        <div class="content-items"></div>
    </div>
    <div class="content-cart-wrap"></div>
    <div class="formWindow"></div>
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
        function closeWindow() {
            $(".formWindow").empty();
            $(".formWindow").css("margin-top","0em");
            $(".formWindow").css("margin-left","0em");
            $(".formWindow").css("height","0em");
            $(".formWindow").css("width","0em");
        }

        $(".content-items").ready(function() {
            ajaxLoadHTMLToElement("unitlst.php","content-items");
        });        
    </script>
  </body>
  </html>