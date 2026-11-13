<html>
    <head>
        <title>EBS | Admin Portal</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://kit.fontawesome.com/77afa21d2d.js" crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://gstatic.com" crossorigin>
        <link href="https://googleapis.com" rel="stylesheet">
        <link rel="icon" href="favicon.png">
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>-->
        <script src="ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="shortcut icon" href="../admin/images/favicon.ico">
        <style>

            /* ===================================
   EBS INVENTORY DASHBOARD THEME
=================================== */

        :root {
            --fa-primary-color: white;   /* Sets primary color for applicable v7 icons */
            --fa-secondary-color: #24465F; /* Sets secondary color for Duotone icons */

            --navy: #193247;
            --navy-dark: #10283D;
            --navy-light: #24465F;

            --teal: #159BB5;
            --teal-hover: #087F99;

            --page-bg: #F5F7FA;
            --surface: #FFFFFF;
            --border: #DCE4EC;

            --font-fam: 'Poppins', sans-serif;
            --text: #243447;
            --muted: #64748B;

            --danger: #E05260;
            --danger-hover: #C83D4C;

            --shadow: 0 3px 12px rgba(25, 50, 71, 0.07);
        }


            /*body {
                user-select: text;
                background-color: #ffffff;
                font-family: 'Poppins', sans-serif;
                margin: 0;
            }*/
            body {
                background: var(--page-bg);
                color: var(--text);
                font-family: var(--font-fam);
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
              background-color: #F5F5F5;
            }
            /* Track */
            ::-webkit-scrollbar-track {
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #F5F5F5; 
            }
             
            /* Handle */
            ::-webkit-scrollbar-thumb {
              background-color: #0ae;
    
              background-image: -webkit-gradient(linear, 0 0, 0 100%,color-stop(.5, rgba(255, 255, 255, .2)),color-stop(.5, transparent), to(transparent)); 
            }

            /* Handle on hover */
            ::-webkit-scrollbar-thumb:hover {
              background: #0ae; 
            }

            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
            }
            @media only screen and (min-width: 1000px) {
                div.content-dashboard {
                    display: flex;
                    flex-direction: row;
                }
            }

            div.main-container {
                //background-image: url("sysbg.png");
                //background-size: cover;
                display: flex;
                flex-direction: row;
                height: 100%;
                width: 100%;
                background-color: #f9f9f9;
                border-radius: 2px 2px 0 0;
            }

            div.container-left {
                background: var(--page-bg);
                min-height: calc(100vh - 50px);
                /*background-color: #ededed;*/
                transition: 0.5s;
                padding: 10px 0px 10px 10px;
                width: 260px;
            }
            div.container-right {
                background: var(--page-bg);
                min-height: calc(100vh - 50px);
                padding: 10px;
                width: 100%;
            }

/* Default menu */
.menu {
    display: flex;
    margin-left: 10px;
    padding-left: 12px;
    padding: 10px 5px;
    text-decoration: none;
    font-size: 11px;
    color: white;
    display: block;
    border-radius:2px;
    margin: 5px;
    display: flex;
    flex-direction: row;
    align-items: center;
    transition: background-color 0.2s, font-weight 0.2s, font-size 0.2s;
}

/* Hover effect */
.menu:hover {
    background-color: #e0e7ff;
    color: #4338ca;
}

/* Active menu */
.menu.active {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #ffffff;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}

/* Optional: active menu icon */
.menu.active .fa{
    color: var(--fa-secondary-color);
}

.menu span:first-child {
    width: 25px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

.menu span:first-child .fa{
    font-size: 18px;
    transition: color 0.3s ease;
}

/* Filter and report toolbar */
.filter-panel {
    padding-left: 10px;
    padding-top: none !important;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 2px;
    padding: 5px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: var(--shadow);
    margin-bottom: 16px;
}

/* Inputs and dropdowns */
.filter-panel input,
.filter-panel select,
.filter-panel .form-control,
.filter-panel .form-select {
    min-height: 36px;
    background-color: #FFFFFF;
    color: var(--text);
    border: 1px solid #CBD5E1;
    border-radius: 5px;
    padding: 7px 10px;
}

.filter-panel input:focus,
.filter-panel select:focus,
.filter-panel .form-control:focus,
.filter-panel .form-select:focus {
    border-color: var(--teal);
    outline: none;
    box-shadow: 0 0 0 3px rgba(21, 155, 181, 0.12);
}

/* General buttons */
.btn {
    border-radius: 5px;
    font-weight: 600;
    transition: 0.2s ease;
}

/* Primary action */
.btn-primary,
.btn-teal {
    background: var(--teal);
    color: #FFFFFF;
    border: 1px solid var(--teal);
}

.btn-primary:hover,
.btn-teal:hover {
    background: var(--teal-hover);
    color: #FFFFFF;
}

/* PDF export */
.btn-pdf {
    background: var(--danger);
    color: #FFFFFF;
    border: 1px solid var(--danger);
    border: none;
    border-radius: 3px;
    height: 35px;
    font-size: 12px;
    transition: background 0.2s ease;
}

.btn-pdf:hover {
    background: var(--danger-hover);
    color: #FFFFFF;
}

/* CSV export */
.btn-csv {
    background: #FFFFFF;
    color: #087F70;
    border: 1px solid #159B83;
}

.btn-csv:hover {
    background: #E9F8F3;
    color: #066B5F;
}

/* Logout */
.btn-logout {
    background: var(--danger);
    color: #FFFFFF;
    border: none;
    border-radius: 5px;
    padding: 11px 15px;
}

.btn-logout:hover {
    background: var(--danger-hover);
    color: #FFFFFF;
}

/* Tables */
.table {
    background: #FFFFFF;
    color: var(--text);
}

.table thead th {
    background: #EAF0F5;
    color: var(--navy);
    border-bottom: 2px solid var(--border);
}

.table td,
.table th {
    border-color: var(--border);
    vertical-align: middle;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--muted);
}


/* Empty state container */
.empty-state {
    min-height: 350px;
    width: 100%;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    text-align: center;
    padding: 40px 20px;

    color: #64748B;
}

/* Circular icon background */
.empty-state-icon {
    position: relative;

    width: 112px;
    height: 112px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 20px;

    border-radius: 50%;

    background: #E7EFFB;
}

/* Document icon */
.empty-state-icon .fa-file-lines {
    font-size: 65px;
    color: #9AB7DA;
}

.empty-state-icon .fa-house {
    font-size: 75px;
    color: #9AB7DA;
}

/* Magnifying glass overlay */
.empty-state-icon .search-overlay {
    position: absolute;

    right: 9px;
    bottom: 12px;

    font-size: 37px;

    color: #456889;

    background: #E7EFFB;
    border-radius: 50%;

    padding: 3px;
}

/* Main message */
.empty-state h3 {
    margin: 0 0 8px;

    font-size: 20px;
    font-weight: 700;

    color: #536B86;
}

/* Supporting message */
.empty-state p {
    margin: 0;

    font-size: 14px;
    line-height: 1.6;

    color: #64748B;
}

/* Responsive sizing */
@media (max-width: 576px) {
    .empty-state {
        min-height: 260px;
        padding: 30px 15px;
    }

    .empty-state-icon {
        width: 90px;
        height: 90px;
    }

    .empty-state-icon .fa-file-lines {
        font-size: 52px;
    }

    .empty-state-icon .fa-house {
        font-size: 52px;
    }

    .empty-state-icon .search-overlay {
        font-size: 30px;
    }

    .empty-state h3 {
        font-size: 18px;
    }
}


/* Responsive filters */
@media (max-width: 768px) {
    .filter-panel {
        align-items: stretch;
        flex-direction: column;
    }

    .filter-panel input,
    .filter-panel select,
    .filter-panel button {
        width: 100%;
    }

    .main-content {
        padding: 10px;
    }
}

            div.menubar {
                background: linear-gradient(180deg,var(--navy),var(--navy-dark));
                color: #FFFFFF;
                overflow: hidden;
                transition: 0.2s;
                height: 100%;
                box-shadow: 8px 8px 7px #aaaaaa;
                border-radius:3px;
                padding: 0px;
            }
            div.menubar div a {
                padding-left: 15px;
                padding: 10px 10px;
                text-decoration: none;
                font-size: 11px;
                color: white;
                display: block;
                border-radius:2px;
                margin: 5px;
                display: flex;
                flex-direction: row;
                align-items: center;
                transition: background-color 0.2s, font-weight 0.2s, font-size 0.2s;
            }
            div.menubar div a:hover {
                background-color: var(--navy-light);
                color: #FFFFFF;
                font-weight: bolder;
                font-size: 12px;
            }
            div.menubar div a.active {
                background: var(--teal);
                color: #FFFFFF;
                border-left: 4px solid #69E4F2;
                font-weight: 600;
            }

            div.menubar div a span img {
                background-color: #1B3045;
                padding: 2px;
                border-radius: 3px;
            }
            
            div.menubar div a span{
                margin-right: 10px;
            }

            div.menubar-top {
                height: 90%;
            }
            div.menubar-logout a{
                background-color: #C9324D;
                padding-left: 20px;
            }
            div.menubar-logout a:hover{
                background-color: #01A3DC;
            }

            div.navbar {
                padding-top:10px;
                padding-bottom:5px;
                background: var(--navy);
                color: #FFFFFF;
                border-radius: 4px;
                box-shadow: var(--shadow);
                height:28px;
                overflow: hidden;
                font-size:14px;
                font-weight: bold;

                /*padding-top:10px;
                padding-bottom:5px;
                color: #ffffff;
                background-color: #1B3045;
                height:21px;
                border-radius: 3px;
                border-bottom:1px solid gray;
                overflow: hidden;
                font-size:14px;
                font-weight: bold; */
            }
            div.navbar a {
                text-decoration: none;
                font-size: 15px;
                padding: 10px 15px 10px 15px;
            }
            div.navbar a.icon {
                background-color: #f6f6f6;
                color: black;
              }
            div.navbar a.icon:hover {
                background-color:#ededed;
            }
            div.navbar span {
                padding-left:10px;
            }

            div.content-login {
                padding-left: 15px;
                white-space: pre-wrap;
                background-color: #35363a;
                margin:auto;
                width:70%;
                height:350;
                border-radius:4px;
            }
            div.content-login label {
                font-family: Calibri;
                font-size: 13px;
                color: white;
            }
            div.content-login input {
                border: 1px solid #ededed;
                border: none;
                border-radius: 2px;
                padding: 4px;
                font-size: 11px;
                width: 250px;
                height:25px;
            }
            div.content-login button {
                border:none;
                border-radius:2px;
                background-color: #00a0e5;
                color:#ffff;
                height:35px;
                font-size:12px;
            }
            div.content-login button:hover {
                background-color: #0073ba;
            }
            div.content-login a {
                font-size: 12px;
                color: white;
            }
            div.content-login input, div.content-login button {
                 width:195px;
            }
            
            div.content-signup {
                margin-top: 1px;
                padding-left: 15px;
                white-space: pre-wrap;
                background-color:white;
            }
            div.content-signup label {
                font-family: Calibri;
                font-size: 13px;
                font-weight: bold;
            }
            div.content-signup input[type=text], div.content-signup input[type=date], div.content-signup input[type=email], div.content-signup input[type=tel], div.content-signup input[type=password] {
                border: 1px solid #ededed;
                border-radius: 2px;
                padding: 4px;
                font-size: 11px;
                height:25px;
            }
            div.content-signup span {
                color: red;
                padding-left: 3px;
                font-size: 12px;
            }
            div.content-signup button {
                border:none;
                border-radius:3px;
                background-color: #00a0e5;
                color:#ffff;
                height:35px;
                font-size:11px;
            }
            div.content-signup button:hover {
                background-color: #0073ba;
            }
            div.content-signup a {
                font-size: 12px;
            }
            div.content-signup input[type=text], div.content-signup input[type=date], div.content-signup input[type=email], div.content-signup input[type=tel], div.content-signup input[type=password],div.content-signup button {
                width: 200px;
            }
            
            div.profile-view label {
                font-family: Calibri;
                font-size: 14px;
                color: #ffffff;
            }
            div.profile-view input {
                border: 1px solid #767676;
                border-radius: 3px;
                padding: 4px;
                font-size: 11px;
                width: 250px;
                height:25px;
            }

            div.content-lbl {
                padding-left: 10px;
                padding-top: 10px;
            }
            div.content-lbl label {
                font-family: Calibri;
                font-size: 13px;
                font-weight: bold;
                color: #506d8c;
            }

            div.content-txt {
                padding-left: 10px;
                padding-top: 10px;
            }
            div.content-txt input {
                border: 1px solid #ededed;
                border-radius: 3px;
                height: 25px;
                width: 150px;
                padding: 4px;
                font-size: 11px;
            }
            
            div.content-btn {
                padding-left: 10px;
                padding-top: 10px;
            }
            /*div.content-btn button {
                border: none;
                border-radius: 3px;
                background-color: #169612;
                color: white;
                height: 35px;
                font-size: 12px;
            }
            div.content-btn button:hover {
                background-color: #127a0f;
                transition: 0.5s;
            }*/
            
            div.content-cbo {
                padding-left: 10px;
                padding-top: 10px;
            }
            div.content-cbo select {
                border-radius: 3px;
                height: 35px;
                width: 80px;
                padding-left:5px;
                font-size: 12px;
            }
            div.content-rdo {
                font-size: 10px;
            } 
            div.content-grid {
                height: 750px;
                overflow-y: auto;
                white-space: pre-wrap;
                padding:10px;
            }
            div.content-grid table {
                font-family: arial, sans-serif;
                font-size: 12px;
                border-collapse: collapse;
                width: auto;
                background: #FFFFFF;
                color: var(--text);
                height: auto;
            }
            div.content-grid thead {
                margin-top: -10px;
            }
            div.content-grid th {
                text-align: center;
                background: var(--navy);
                color: white;
                border-bottom: 2px solid var(--border);
            }
            div.content-grid td {
                text-align: left;
                background-color: #f2f2f2;
                border: none;
                background-color: #f2f2f2;
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
            /*div.content-grid tr:nth-child(even){
                background-color: #fcf5d7;
            }
            div.content-grid tr:nth-child(odd){
                background-color: #f2f2f2;
            }*/
            div.content-grid tr.t_det:hover{
                background-color: #d6ae56;
                //background-color: #a1852a;
                transition: 0.5s;
            }
            #frmdet{
                background-color: #004860;
                border-radius: 5px;
                transition:0.2s;
                position:fixed;
                top: 50%;
                left: 50%;
                width:0em;
                height:35em;
                margin-top: -17em; /*set to a negative number 1/2 of your height*/
                margin-left: -15em; /*set to a negative number 1/2 of your width*/
            }
            div.detview {
                background-color: #f3f3f3;
                border-radius: 2px;
                white-space: pre-wrap;
                margin: 0px 25px 0px 25px;
                font-size: 12px;
            }
            div.detview textarea {
                //border: none;
                border-radius: 3px;
                resize: none;
                width: 250px;
                height:60px;
                font-family: Arial, Helvetica, sans-serif;
                padding: 4px;
                font-size: 11px
            }
            div.detview label {
                font-family: Calibri;
                font-size: 13px;
                font-weight: bold;
                color: #506d8c;
            }
            div.detview input {
                border: 1px solid #ededed;
                border-radius: 2px;
                height: 25px;
                padding: 4px;
                font-size: 11px;
            }
            div.detview button {
                border:none;
                border-radius:3px;
                background-color: #04aa6d;
                color:white;
                height:30px;
                width: auto;
                min-width: 55px;
                font-size:12px;
            }
            
            div.detview table {
                font-family: arial, sans-serif;
                font-size: 11px;
                border-collapse: collapse;
                width: auto;
            }

            div.detview caption, th {
                text-align: center;
                font-weight:bold;
                background-color: #f2f2f2;
            }
            div.detview td {
                text-align: left;
            }
            div.detview caption, th, td {
                border: 1px solid #dddddd;
                padding: 8px;
            }          
            div.detview tr.t_det:hover{
                background-color: #a1852a;
            }
            
            div.detview select {
                border-radius: 3px;
                height: 35px;
                width:70px;
                padding-left:10px;
                font-size: 12px;
            }
            
            
            div.content-notif {
                overflow-y: auto;
                max-height: 560px;
                padding: 10px;
                
            }
            div.content-notif div.row-notif {
                background-color: white;
                width: auto;
                height:auto;
                max-width: 300px;
                max-height:100px;
                border-radius: 4px;
                padding: 10px;
                margin-bottom: 3px;
            }
            div.content {
                margin-bottom: 20px;
            }
            div.content span {
                font-size:12px;
            }
            div.datetime span {
                color:gray;
                font-size:10px;
            }

            div.content-dashboard {
                height: 500px;
                overflow-y: auto;
            }

            div.content-dashboard div.content-dashboard-div {
                margin: 10px;
                padding: 20px;
                background-color: #f6f6f6;
                border-radius: 5px;
                width: auto;
                height: 150px;
                box-shadow: 1px 1px 12px #aaaaaa;
            }
            div.content-dashboard-div div h4, div.content-dashboard-div div input[type=text]{
                margin-left: 5px;
                margin-right: 5px;
            }
    </style>
  </head>
  <body>