<?php
defined('_JEXEC') or die;
$app = JFactory::getApplication();
?>
<!DOCTYPE html>
<html lang="ru">
<head>

    <meta charset="utf-8">
    <jdoc:include type="head" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/bootstrap/css/bootstrap.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/bootstrap/css/bootstrap-responsive.css" type="text/css" media="screen" />


    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300&subset=latin,cyrillic' rel='stylesheet' type='text/css'>


    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/owl.carousel.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/owl.theme.css" type="text/css" media="screen" />


    <!-- bxSlider CSS file -->
    <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/jquery.bxslider/jquery.bxslider.css" rel="stylesheet" />


    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/owl.carousel.js"></script>


    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" media="screen" />

    <!-- ...  ... -->


    <script type="text/javascript">
        jQuery(document).ready(function($) {

            $("#owl-demo").owlCarousel({


                navigation : true
            });

        });

    </script>

    <script type="text/javascript">jQuery.noConflict();</script>



    <link rel="apple-touch-icon-precomposed" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/bootstrap/ico/apple-touch-icon-57-precomposed.png">
</head>
<body >


<div class="container-fluid top">
    <div class="container">

        <div class="span12">
            <div class="row">
                <div class="span11"></div>
                <div class="menutop navbar span11">
                    <div class="navbar-inner">
                        <jdoc:include type="modules" name="topmenu" style="none" />
                    </div>
                </div><!--End navbar-->

            </div>
        </div>
    </div>
</div>



<div class="container-fluid header">

    <div class="container">
        <div class="span12">
                <div class="row">
                    <div class="span1"></div>
                    <div class="span3 emblem_logo">
                    </div>
                    <div class="span8 name">
                                <h1>ZWEI<span class="bold">FACH</span></h1>
                                <h4>Gemeinschaftsbüro Deinis & Kaczmarek</h4>
                    </div>

                </div>

            <div class="row service">
                <div class="span1"></div>
                <div class="span11">




                    <jdoc:include type="modules" name="service" style="none" />
                </div>
            </div>



        </div>
    </div>
</div>





<!--<div class="container-fluid upper">
    <div class="container">
        <div class="span12 slider">
            <div class="row ">
                        <ul class="bxslider">
                            <li class="slider_image">
                                    <img src="/images/slider/geo.jpg" />
                                    <span class="hidden-phone"><h3><a href="#">Продукция</a></h3>
                                        <ul class="list_img hidden-tablet hidden-phone">
                                            <li><a href="#">Картографические материалы (аналоговые)</a></li>
                                            <li><a href="#">Дистанционное зондирование Земли</a></li>
                                            <li><a href="#">Гео материалы</a></li>
                                            <li><a href="#">Картографические материалы (векторные)</a></li>
                                        </ul>
                                    </span>
                            </li>
                            <li class="slider_image">
                                <img src="/images/slider/geo_2.png" />
                                    <span class="hidden-phone"><h3><a href="#">Услуги</a></h3>
                                        <ul class="list_img hidden-tablet hidden-phone">
                                            <li><a href="#">Изготовление карт материалов</a></li>
                                            <li><a href="#">Инженерно-геодезические изыскания</a></li>
                                            <li><a href="#">Проверки и ремонт оборудования</a></li>
                                            <li><a href="#">Подготовка карт к изданию</a></li>
                                        </ul>
                                    </span>
                            </li>

                        </ul>
                        <jdoc:include type="modules" name="topslider" style="xhtml" />
            </div>
        </div>
    </div>
</div> -->


<div class="wrap-content container-fluid">
    <div class="container">
         <div class="breadcrumbs row">
            <div class="span12">
             <jdoc:include type="modules" name="breadcrumb" style="xhtml" />
             </div>
         </div>

         <div class="content row">
             <div class="position0 span12"> <jdoc:include type="modules" name="position0" style="xhtml" /></div>
             <div class="span12"><jdoc:include type="component" /></div>
         </div>

        <div class="row">
            <div class="after-content span12"> <jdoc:include type="modules" name="after_content" style="none" /></div>
        </div>
    </div>
</div>


<div class="wrap-content container-fluid">
    <div class="container">
        <div class="row">
            <div class="clients span12"> <jdoc:include type="modules" name="position1" style="xhtml" /></div>
        </div>
    </div>
</div>

<div class="container-fluid position5">
            <div class=""> <jdoc:include type="modules" name="position5" style="xhtml" /></div>
</div>

<div class="container-fluid footer">
<div class="container">
	 <div class="row">
         <a href="/"><div class="logo-footer span4">    </div></a>
         <div class="footer-menu span8"> <jdoc:include type="modules" name="footer-menu" style="none" />
         </div>

         <div class="right">Copyright © 2012 - 2015 ZWEIFACH
             Gemeinschaftsbüro Deinis & Kaczmarek</div>
         <div class="design">developed by - Barkalov Anton</div>
     </div>
</div>
</div>


<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/jquery.js"></script>
<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/scroll/js/jquery.scrollUp.js"></script>
<!-- bxSlider Javascript file -->
<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/jquery.bxslider/jquery.bxslider.min.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('.bxslider').bxSlider({
            auto: true,
            autoControls: true,
            adaptiveHeight: true,
            mode: 'fade',
            pause: 10000,
            speed: 500
        });
    });
</script>

<script type="text/javascript">
$(function () {
    $.scrollUp({
        scrollName: 'scrollUp', //  ID элемента
        topDistance: '300', // расстояние после которого появится кнопка (px)
        topSpeed: 300, // скорость переноса (миллисекунды)
        animation: 'fade', // вид анимации: fade, slide, none
        animationInSpeed: 200, // скорость разгона анимации (миллисекунды)
        animationOutSpeed: 200, // скорость торможения анимации (миллисекунды)
        scrollText: ' ↑ ', // текст
        activeOverlay: false // задать CSS цвет активной точке scrollUp, например: '#00FFFF'
    });
});
</script>

</body>
</html>
