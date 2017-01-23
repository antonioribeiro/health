<style>
    body {
        background-color: #fff;
        padding-top: {{ 30 * config('health.style.multiplier', 1) }}px;
        padding-bottom: {{ 20 * config('health.style.multiplier', 1) }}px;
        font-family: 'Ubuntu', sans-serif;
    }

    div.panel > h1, h2, h3 {
        line-height: 1.0em;
    }

    div.panel > h1, h3 {
        font-size: {{ 3.5 * config('health.style.multiplier', 1) }}em;
    }

    div.panel > .h1, .h2, .h3, .h4, body, h1, h2, h3, h4, h5, h6 {
        font-family: Roboto,Helvetica,Arial,sans-serif;
        font-weight: 500;
        line-height: 1.0em;
        margin-top: 11px;
    }

    div.panel > .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
        padding-right: 5px;
        padding-left: 5px;
    }

    div.panel > div.btn.single h3 {
        margin: 0;
    }

    div.panel > h2 {
        font-size: {{ 4.5 * config('health.style.multiplier', 1) }}em;
    }

    div.panel > .btn {
        margin-top: 0px !important;
    }

    div.panel > .title {
        padding: 8px;
        margin-bottom: 20px;
    }

    div.panel > .btn {
        margin-top: {{ 25 * config('health.style.multiplier', 1) }}px;
        min-height: {{ 5 * config('health.style.multiplier', 1) }}px;
    }

    div.panel > .btn.single {
        text-align: left;
    }

    div.panel > .btn:not(.btn-just-icon):not(.btn-fab) .fa, .navbar .navbar-nav>li>a.btn:not(.btn-just-icon):not(.btn-fab) .fa {
        font-size: {{ 3.5 * config('health.style.multiplier', 1) }}em;
    }

    @media (min-width: 992px){
        .typo-line{
            padding-left: 140px;
            margin-bottom: 40px;
            position: relative;
        }

        .typo-line .category{
            transform: translateY(-50%);
            top: 50%;
            left: 0px;
            position: absolute;
        }
    }

    #map{
        position:relative;
        width:100%;
        height: calc(100% - 60px);
        margin-top: 70px;
    }

    .places-buttons .btn{
        margin-bottom: 30px
    }

    .space-70{
        height: 70px;
        display: block;
    }

    .sidebar .nav > li.active-pro{
        position: absolute;
        width: 100%;
        bottom: 10px;
    }

    .tim-row{
        margin-bottom: 20px;
    }

    .tim-typo{
        padding-left: 25%;
        margin-bottom: 40px;
        position: relative;
    }
    .tim-typo .tim-note{
        bottom: 10px;
        color: #c0c1c2;
        display: block;
        font-weight: 400;
        font-size: 13px;
        line-height: 13px;
        left: 0;
        margin-left: 20px;
        position: absolute;
        width: 260px;
    }
    .tim-row{
        padding-top: 50px;
    }
    .tim-row h3{
        margin-top: 0;
    }
</style>
