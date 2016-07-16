<?php
/**
 * User: Mike
 * Date: 7/13/13
 * Time: 8:11 PM
 */
?>

<div id="wrapper">
    <div id="TopWelcomeBanner">
        <div class="TopWelcomeBannerStrip" id="TopWelcomeStrip">
        </div>
        <div id="TopWelcomeStripTextContainer">
            <div id="TopWelcomeStripTitle">
                <span id="TopWelcomeStripTextTitle">Welcome to ScorePortal!</span>
            </div>
            <div id="TopWelcomeStripDescription">
                <span id="TopWelcomeStripTextDescription">Gain insight into your education and grades never thought possible before.</span>
            </div>
        </div>
        <a onclick="ga('send', 'event', 'Index', 'Click', 'Play Video');"
           href="//fast.wistia.net/embed/iframe/h4ioo71arq?popover=true"
           class="wistia-popover[height=480,playerColor=7b796a,width=850]">
            <script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/popover-v1.js"></script>
            <div id="TopWelcomeStripWatchVideo" data-extended="false">
                <div class="TopWelcomeBannerStrip" id="TopWelcomeWatchVideoStrip">
                </div>
                <span
                    style="position:absolute;width:100%;height:100%;top:0;left: 0;z-index: 1;background-image: url('/images/blank.gif');"></span>
        </a>
        <span id="TopWelcomeStripWatchVideoText">Watch Video</span>

        <div id="TopWelcomeStripWatchVideoPlayButton">
        </div>
    </div>
    </a>
    <!--        <div id="TopWelcomeStripDemo" data-extended="false">-->
    <!--            <div class="TopWelcomeBannerStrip" id="TopWelcomeDemoStrip">-->
    <!--            </div>-->
    <!--            <a href="#Demo"><span style="position:absolute;width:100%;height:100%;top:0;left: 0;z-index: 1;background-image: url('/images/blank.gif');"></span></a>-->
    <!--            <span id="TopWelcomeStripDemoText">Try Demo</span>-->
    <!--            <div id="TopWelcomeStripDemoInformationButton">-->
    <!--            </div>-->
    <!--        </div>-->
</div>
<div id="LeftColumn">
    <table id="FeaturesList">
        <tr>
            <td>
                <img src="/images/icons/chart_up_color.png">
            </td>
            <td class="Text">
                <span class="Title">Grade History - </span><span class="Description">Track your progress in your classes.</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/award_star_gold_3.png">
            </td>
            <td class="Text">
                <span class="Title">Percentile - </span><span class="Description">See how your performance stacks up among your peers.</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/chart_column_2.png">
            </td>
            <td class="Text">
                <span class="Title">Category Breakdown - </span><span class="Description">Visualize your strengths and weaknesses.</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/pencil.png">
            </td>
            <td class="Text">
                <span class="Title">Project Grades - </span><span class="Description">Add and modify grades virtually to project progress.</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/chart_line.png">
            </td>
            <td class="Text">
                <span class="Title">Class Average - </span><span class="Description">Watch how your grades trend with the class.</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/SearchIcon.png">
            </td>
            <td class="Text">
                <span class="Title">Search - </span><span
                    class="Description">Smart search all assignments and classes.</span>
            </td>
        </tr>
        <tr>

        </tr>
        <tr>
            <td>
                Support ScorePortal!
            </td>
            <td>
                <div class="DonateButton">
                    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=DELVGAGH392HS&lc=US&item_name=ScorePortal&item_number=scoreportaldevteam&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted"
                       target="_blank"> <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif"></a>
                </div>
                <div class="DonateButton">
                    <a class="coinbase-button" data-code="a1daafcc6d36d88fe7934e1e4a6ca25c"
                       data-button-style="donation_small"
                       href="https://coinbase.com/checkouts/a1daafcc6d36d88fe7934e1e4a6ca25c">Donate Bitcoins</a>
                    <script src="https://coinbase.com/assets/button.js" type="text/javascript"></script>
                </div>
                <div class="DonateButton">
                    <div class='doge-widget-wrapper'>
                        <form method='get' action='https://www.dogeapi.com/checkout'>
                            <input type='hidden' name='widget_type' value='donate'>
                            <input type='hidden' name='widget_key' value='186dx66hzl3ao1ipxrasadfnp2'>
                            <input type='hidden' name='payment_address' value='DHLpRhGPKwBVpwgmm94LZHxZuvUAw4PnUA'>

                            <div class='doge-widget' style='display:none;'></div>
                        </form>
                    </div>
                </div>
                <div class="DonateButton">
                    <script>
                        CoinWidgetCom.go({
                            wallet_address: "LWPBU2ssZoojdf3FyTVtCD96aWrXqffvQG", currency: "litecoin", counter: "hide", alignment: "bl", qrcode: false, auto_show: false, lbl_button: "Donate Litecoin", lbl_address: "ScorePortal Litecoin Address:", lbl_count: "donations", lbl_amount: "LTC"
                        });
                    </script>
                </div>
            </td>
        </tr>
        <!--
        <tr>
            <td>
                <img src="/images/icons/star.png">
            </td>
            <td class="Text">
                <span class="Title">Difficulty Index - </span><span class="Description">Be informed of class difficulty in course selection.</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/user_comment.png">
            </td>
            <td class="Text">
                <span class="Title">Student Reviews - </span><span class="Description">Read and give peer reviews for classes.</span>
            </td>
        </tr>
        -->
    </table>
    <!--    <div id="DemoLink">-->
    <!--        Not convincing enough? <a href="#">Click here</a> to try a demo!-->
    <!--    </div>-->
</div>
<div id="RightColumn">
    <div id="RightColumnTitle">
        Sign Up or Log In
    </div>
    <div id="JavascriptWarning" style="text-align:center; margin-top: 15px;">
        Please enable Javascript to use this site.
    </div>
    <div id="my-signin2"></div>
    <!--
    <div id="ConnectButtonsContainer" style="display:none;">
        <a href="#Yahoo">
            <div class="ConnectButtons" id="YahooConnectButton">
                <div class="ConnectButtonLogo">

                </div>
                <div class="ConnectButtonText">
                    <span>Connect using </span><span class="ConnectServiceName">Yahoo</span>
                </div>
            </div>
        </a>
    </div>
    -->
    <div class="fb-like" data-href="https://www.facebook.com/ScorePortal" data-send="true" data-layout="button_count"
         data-width="300" data-show-faces="false"></div>
    <div class="fb-facepile" data-href="https://www.facebook.com/ScorePortal" data-max-rows="7" data-size="small"
         data-width="330"
    ">
</div>
</div>
</div>