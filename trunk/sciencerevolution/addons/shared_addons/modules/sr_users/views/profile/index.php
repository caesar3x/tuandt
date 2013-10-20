<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/12/13
 */
?>
<div class="col-main">
<div class="wrap-user-detail">
    <div class="clear user-detail">
        <img src="{{url:site}}media/demo/avata1.jpg" class="avata"/>
        <div class="user-info">
            <div class="value clear">
                <div class="score">Score <br/><span>201</span></div>
                <div class="follower">Follower<br /><span>20.1k</span></div>
                <div class="t-download">Total download<br /><span>37.9k</span></div>
                <div class="average">
                    <span>Average downloads</span><strong>24.9k</strong><br />
                    <span>Average score</span><strong>5.7k</strong>
                </div>
            </div>
            <h2 class="name"> Anna Paklova<a href="#"><i class="icon-pencill"></i></a></h2>
            <p><span class="job">Material Engineers</span> <span class="usercode">User code: ME5498</span></p>
            <p>Current company : <strong>Mjet</strong></p>
            <p>
                <span class="mobile">879-8468-8765</span>
                <span class="line">|</span>
                <span class="tel">879-8468-8765</span>
                <span class="line">|</span>
                <span class="location">Sydney</span>
                <span class="line">|</span>
                <span class="national" style="background-image:url({{url:site}}media/flags/au.png)">Australia</span>
            </p>
            <p><span class="birthday">Day of birth: 11 - August - 1985</span></p>
        </div>
    </div>
    <div class="more-info">
        <div class="wrap-minfo">
            <div class="col col1">
                <div class="box-nav box-lang">
                    <div class="box-title">
                        <span>Languages</span>
                        <a href="#" ><i class="icon-edit"></i></a>
                    </div>
                    <div class="box-content">
                        <p class="national" style="background-image:url({{url:site}}media/flags/england.png)">English <span>/ Mother Language</span></p>
                        <p class="national" style="background-image:url({{url:site}}media/flags/fr.png)">French <span>/ Proficient</span></p>
                        <p class="national" style="background-image:url({{url:site}}media/flags/ru.png)">Russia <span>/ Basic</span></p>
                    </div>
                </div>
                <div class="box-nav box-edu">
                    <div class="box-title">
                        <span>Education</span>
                        <a href="#" ><i class="icon-edit"></i></a>
                    </div>
                    <div class="box-content">
                        <p>
                            <strong class="eduname">Bachelor’s degree</strong> in <strong>Material Engineering</strong><br />
                            <span class="time">2007 May / Troy University</span>
                        </p>
                        <p>
                            <strong class="eduname">Mastre's degree</strong> in <strong>Material Engineering</strong><br />
                            <span class="time">2011 May / Larie University</span>
                        </p>
                        <p>
                            <strong class="eduname">Haverch’s degree</strong> in <strong>Material Engineering</strong><br />
                            <span class="time">2013 May / Troy University</span>
                        </p>
                    </div>
                    `								</div>
            </div>
            <div class="col col2">
                <div class="box-nav box-skill">
                    <div class="box-title">
                        <span>Skill</span>
                        <a href="#" ><i class="icon-edit"></i></a>
                    </div>
                    <div class="box-content">
                        <p>- iPhone applications development</p>
                        <p>- Android applications development</p>
                        <p>- Blackberry applications development</p>
                        <p>- Mac OS X software development</p>
                        <p>- Web programming, Webservice API</p>
                        <p>- Database development</p>
                        <p>- Mac OS X software development</p>
                        <p>- Blackberry applications development</p>
                        <p>- Web programming, Webservice API</p>
                        <p>- iPhone applications development</p>
                        <p>- Android applications development</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function($){
            $(window).load(function(){
                $(".wrap-minfo").mCustomScrollbar();
            });
        })(jQuery);
    </script>
</div>
<div class="timeline">
    <div class="title">
        <h3>Career timeline</h3>
        <div class="filter">
            <span class="checkbox"><input type="checkbox" name="Trainning"></span>Trainning
            <span class="checkbox"><input type="checkbox" name="Education"></span>Education
            <span class="checkbox"><input type="checkbox" name="Education"></span>Award
            <span class="checkbox"><input type="checkbox" name="Job"></span>Job
            <span class="checkbox"><input type="checkbox" name="Job"></span>Publication
            <span class="checkbox"><input type="checkbox" name="All"></span> Display all
        </div>
    </div>
    <div class="time-year">
        <span class="mark tyear" style="top:20px">2013</span>
        <span class="mark tyear" style="top:120px">2012</span>
        <span class="mark tyear" style="top:930px">2011</span>

        <div class="time-month">
            <div class="time-now">Now</div>
            <span class="mark tmonth active" style="top:20px">Jan' 13</span>
            <span class="mark tmonth" style="top:120px">Dec' 12</span>
            <span class="mark tmonth" style="top:140px">Oct' 12</span>
            <span class="mark tmonth" style="top:160px">Sep' 12</span>
            <span class="mark tmonth active" style="top:180px">Aug' 12</span>

            <span class="mark tmonth" style="top:930px">May' 1</span>
            <span class="mark tmonth" style="top:950px">Apr' 1</span>
            <span class="mark tmonth" style="top:970px">Mar' 1</span>
            <span class="mark tmonth" style="top:990px">Feb' 1</span>
            <span class="mark tmonth active" style="top:1010px">Jan' 1</span>


            <div class="update-box green">
                <div class="update-title">
                    <p><strong>Award:</strong> Best author of the month - January<i class="icon-setting"></i><i class="icon-pencilw"></i><span class="right">12-jun-2013</span></p>
                    <div class="markicon"><i class="icon-cup"></i></div>
                </div>
                <div class="book-item clear">
                    <div class="book-image">
                        <a href="#" title="title"><img class="bookimg" src="{{url:site}}media/demo/book1.jpg" alt="title"/></a>

                    </div>
                    <div class="book-infor ">
                        <div class="bookname undefine">
                            <p><a href="{{url:site}}view-article">Award issued by Sandy Ford mariya</a></p>
                        </div>
                        <p class="des">
                            Phosfluorescently pursue maintainable content without go forward opportunities. Intrinsicly exploit functionalized quality vectors vis-a-vis user-centric applications. Professionally mesh covalent services through
                        </p>
                    </div>
                </div>
            </div>

            <div class="update-box orange">
                <div class="update-title">
                    <p><strong>Job position start:</strong> HCL Technologies<i class="icon-setting"></i><i class="icon-pencilw"></i><span class="right">12-jun-2013</span></p>
                    <div class="markicon"><i class="icon-bag"></i></div>
                </div>
                <div class="book-item clear">
                    <div class="book-image">
                        <a href="#" title="title"><img class="bookimg" src="{{url:site}}media/demo/book1.jpg" alt="title"/></a>

                    </div>
                    <div class="book-infor ">
                        <div class="bookname undefine">
                            <p><a href="{{url:site}}view-article">Composites and Thermoplastics</a></p>
                        </div>
                        <p class="des">
                            <strong>Position: </strong>Senior<br/>
                            <strong>Job Description: </strong><br /> Credibly drive B2B manufactured products via interoperable relationships. Seamlessly foster client-centered channels via an expanded array of bandwidth. Dramatically exploit backend ideas via enabled value. Rapidiously strategize open-source technologies rather than end-to-end
                        </p>
                    </div>
                </div>
            </div>

            <div class="update-box">
                <div class="update-title">
                    <p><strong>Publised:</strong> The immortal life of henrietta lacks <i class="icon-setting"></i><i class="icon-pencilw"></i><span class="right">12-jun-2013</span></p>
                    <div class="markicon"><i class="icon-science"></i></div>
                </div>
                <div class="book-item clear">
                    <div class="book-image">
                        <a href="#" title="title"><img class="bookimg" src="{{url:site}}media/demo/book1.jpg" alt="title"/></a>
                        <div class="ratings">
                            <div class="rating-box">
                                <div style="width:90%" class="rating"></div>
                            </div>
                        </div>
                        <div class="price-box">
                            <span class="price-label">Price :</span>
                            <span class="price">$15</span>
                        </div>
                        <img class="type" src="{{url:site}}media/chemical.png" />
                    </div>
                    <div class="book-infor ">
                        <div class="bookname book-percent">
                            <p><a href="{{url:site}}view-article">Composites and Thermoplastics</a></p>

                            <p class="">Medicine / Genetics / Technical Report</p>
                        </div>
                        <p class="statistic">
                            <span><span class="value">2.15k</span> Score | </span>
                            <span><span class="value">5418</span> Follower | </span>
                            <span><span class="value">87954</span> Download </span>
                        </p>
                        <p class="des">Robert Pavilon 40% / Rin Kurosaki 60%</p>
                    </div>
                </div>
            </div>

            <div class="update-box green">
                <div class="update-title">
                    <p><strong>Award:</strong> Best author of the month - January<i class="icon-setting"></i><i class="icon-pencilw"></i><span class="right">12-jun-2013</span></p>
                    <div class="markicon"><i class="icon-cup"></i></div>
                </div>
                <div class="book-item clear">
                    <div class="book-image">
                        <a href="#" title="title"><img class="bookimg" src="{{url:site}}media/demo/book1.jpg" alt="title"/></a>

                    </div>
                    <div class="book-infor ">
                        <div class="bookname undefine">
                            <p><a href="{{url:site}}view-article">Award issued by Sandy Ford mariya</a></p>
                        </div>
                        <p class="des">
                            Enthusiastically fashion virtual e-tailers before adaptive supply chains. Uniquely coordinate multimedia based deliverables vis-a-vis enterprise niches. Conveniently coordinate top-line web-readiness via multifunctional methods of empowerment. Energistically transition out-of-the-box channels before corporate ROI. Dramatically promote prospective paradigms whereas just in time vortals.
                            <br /> <br />
                            Assertively conceptualize unique web services and excellent applications. Monotonectally supply focused ideas through efficient alignments. Authoritatively pursue highly efficient results and multidisciplinary
                        </p>
                    </div>
                </div>
            </div>

            <div class="update-box pink">
                <div class="update-title">
                    <p><strong>Job position start:</strong> HCL Technologies<i class="icon-setting"></i><i class="icon-pencilw"></i><span class="right">12-jun-2013</span></p>
                    <div class="markicon"><i class="icon-bag"></i></div>
                </div>
                <div class="book-item clear">
                    <div class="book-image">
                        <a href="#" title="title"><img class="bookimg" src="{{url:site}}media/demo/book1.jpg" alt="title"/></a>

                    </div>
                    <div class="book-infor ">
                        <div class="bookname undefine">
                            <p><a href="{{url:site}}view-article">Composites and Thermoplastics</a></p>
                        </div>
                        <p class="des">
                            <strong>Position: </strong>Senior<br/>
                            <strong>Job Description: </strong><br /> Credibly drive B2B manufactured products via interoperable relationships. Seamlessly foster client-centered channels via an expanded array of bandwidth. Dramatically exploit backend ideas via enabled value. Rapidiously strategize open-source technologies rather than end-to-end
                        </p>
                    </div>
                </div>
            </div>

            <div class="update-box">
                <div class="update-title">
                    <p><strong>Publised:</strong> The immortal life of henrietta lacks <i class="icon-setting"></i><i class="icon-pencilw"></i><span class="right">12-jun-2013</span></p>
                    <div class="markicon"><i class="icon-science"></i></div>
                </div>
                <div class="book-item clear">
                    <div class="book-image">
                        <a href="#" title="title"><img class="bookimg" src="{{url:site}}media/demo/book1.jpg" alt="title"/></a>
                        <div class="ratings">
                            <div class="rating-box">
                                <div style="width:90%" class="rating"></div>
                            </div>
                        </div>
                        <div class="price-box">
                            <span class="price-label">Price :</span>
                            <span class="price">$15</span>
                        </div>
                        <img class="type" src="{{url:site}}media/chemical.png" />
                    </div>
                    <div class="book-infor ">
                        <div class="bookname book-percent">
                            <p><a href="{{url:site}}view-article">Composites and Thermoplastics</a></p>

                            <p class="">Medicine / Genetics / Technical Report</p>
                        </div>
                        <p class="statistic">
                            <span><span class="value">2.15k</span> Score | </span>
                            <span><span class="value">5418</span> Follower | </span>
                            <span><span class="value">87954</span> Download </span>
                        </p>
                        <p class="des">Robert Pavilon 40% / Rin Kurosaki 60%</p>
                    </div>
                </div>
            </div>





        </div>
    </div>
</div>
</div>
<div class="col-right bg1">
<div class="tabs">
    <ul class="clear" id="tabs">
        <li class="active first forum"><a href="#">Personal forum</a><span class="active">1</span></li>
        <li class="chat"><a href="#">Chat</a><span>0</span></li>
        <li class="last message"><a href="#">Private message</a><span>0</span></li>
    </ul>

    <div class="block latest-forums">
        <div class="block-title">
            <span>latest forums</span>
        </div>
        <div  class="block-content">
            <ul class="list-post">
                <li>
                    <span class="number">125</span>
                    <p><strong>Primitive fish could nod but not shake its head. Seamlessly exploit</strong></p>
                    <p>Physical and Engineering</p>
                </li>
                <li>
                    <span class="number">382</span>
                    <p><strong>Globally engineer excellent portals whereas standards compliant </strong></p>
                    <p>Biology/Organism</p>
                </li>
                <li>
                    <span class="number">298</span>
                    <p><strong>Completely iterate customer directed e-services before vertical initiatives ?</strong></p>
                    <p>Physical and Engineering</p>
                </li>
            </ul>
        </div>
    </div>

</div>



<div class="block block-documents">
    <div class="block-title">
        <span>ANNA’S documents</span>
        <a href="#" class="more">View more</a>
    </div>
    <div  class="block-content">
        <ul class="book-list">
            <li class="book-item">
                <div class="book-image">
                    <a href="#" title="title"><img src="{{url:site}}media/demo/book1.jpg" alt="title"/></a>
                    <div class="ratings">
                        <div class="rating-box">
                            <div style="width:90%" class="rating"></div>
                        </div>
                    </div>
                    <div class="price-box">
                        <span class="price-label">Price :</span>
                        <span class="price">$15</span>
                    </div>
                    <img class="type" src="{{url:site}}media/chemical.png" />
                </div>
                <div class="book-infor">
                    <div class="bookname book-percent">
                        <h2><a href="{{url:site}}view-article">A Short History of Nearly Everything</a></h2>
                        <p>Medicine / Genetics / Technical Report</p>
                    </div>
                    <p class="statistic">
                        <span><span class="value">2.15k</span> Score | </span>
                        <span><span class="value">5418</span> Follower | </span>
                        <span><span class="value">87954</span> Download </span>
                    </p>
                    <p class="des">
                        Robert Pavilon 40% / Rin Kurosaki 60%
                    </p>
                </div>
            </li>
            <li class="book-item">
                <div class="book-image">
                    <a href="#" title="title"><img src="{{url:site}}media/demo/book2.jpg" alt="title"/></a>
                    <div class="ratings">
                        <div class="rating-box">
                            <div style="width:90%" class="rating"></div>
                        </div>
                    </div>
                    <div class="price-box">
                        <span class="price-label">Price :</span>
                        <span class="price">$15</span>
                    </div>
                    <img class="type" src="{{url:site}}media/earth.png" />
                </div>
                <div class="book-infor">
                    <div class="bookname book-pencil">
                        <h2><a href="{{url:site}}view-article">A Brief History of Time</a></h2>
                        <p>Medicine / Genetics / Technical Report</p>
                    </div>
                    <p class="statistic">
                        <span><span class="value">2.15k</span> Score | </span>
                        <span><span class="value">5418</span> Follower | </span>
                        <span><span class="value">87954</span> Download </span>
                    </p>
                    <p class="des">
                        Robert Pavilon 40% / Rin Kurosaki 10% / Anna Krosalova 10% / Zaodyeck 40%
                    </p>
                </div>
            </li>
            <li class="book-item">
                <div class="book-image">
                    <a href="#" title="title"><img src="{{url:site}}media/demo/book3.jpg" alt="title"/></a>
                    <div class="ratings">
                        <div class="rating-box">
                            <div style="width:90%" class="rating"></div>
                        </div>
                    </div>
                    <div class="price-box">
                        <span class="price-label">Price :</span>
                        <span class="price">$15</span>
                    </div>
                    <img class="type" src="{{url:site}}media/earth.png" />
                </div>
                <div class="book-infor">
                    <div class="bookname book-search">
                        <h2><a href="{{url:site}}view-article">The Selfish Gene</a></h2>
                        <p>Medicine / Genetics / Technical Report</p>
                    </div>
                    <p class="statistic">
                        <span><span class="value">2.15k</span> Score | </span>
                        <span><span class="value">5418</span> Follower | </span>
                        <span><span class="value">87954</span> Download </span>
                    </p>
                    <p class="des">
                        Robert Pavilon 40% / Rin Kurosaki 10% / Anna Krosalova 10% / Zaodyeck 40%
                    </p>
                </div>
            </li>
            <li class="book-item">
                <div class="book-image">
                    <a href="#" title="title"><img src="{{url:site}}media/demo/book1.jpg" alt="title"/></a>
                    <div class="ratings">
                        <div class="rating-box">
                            <div style="width:90%" class="rating"></div>
                        </div>
                    </div>
                    <div class="price-box">
                        <span class="price-label">Price :</span>
                        <span class="price">$15</span>
                    </div>
                    <img class="type" src="{{url:site}}media/chemical.png" />
                </div>
                <div class="book-infor">
                    <div class="bookname book-percent">
                        <h2><a href="{{url:site}}view-article">A Short History of Nearly Everything</a></h2>
                        <p>Medicine / Genetics / Technical Report</p>
                    </div>
                    <p class="statistic">
                        <span><span class="value">2.15k</span> Score | </span>
                        <span><span class="value">5418</span> Follower | </span>
                        <span><span class="value">87954</span> Download </span>
                    </p>
                    <p class="des">
                        Robert Pavilon 40% / Rin Kurosaki 60%
                    </p>
                </div>
            </li>
            <li class="book-item">
                <div class="book-image">
                    <a href="#" title="title"><img src="{{url:site}}media/demo/book2.jpg" alt="title"/></a>
                    <div class="ratings">
                        <div class="rating-box">
                            <div style="width:90%" class="rating"></div>
                        </div>
                    </div>
                    <div class="price-box">
                        <span class="price-label">Price :</span>
                        <span class="price">$15</span>
                    </div>
                    <img class="type" src="{{url:site}}media/earth.png" />
                </div>
                <div class="book-infor">
                    <div class="bookname book-pencil">
                        <h2><a href="{{url:site}}view-article">A Brief History of Time</a></h2>
                        <p>Medicine / Genetics / Technical Report</p>
                    </div>
                    <p class="statistic">
                        <span><span class="value">2.15k</span> Score | </span>
                        <span><span class="value">5418</span> Follower | </span>
                        <span><span class="value">87954</span> Download </span>
                    </p>
                    <p class="des">
                        Robert Pavilon 40% / Rin Kurosaki 10% / Anna Krosalova 10% / Zaodyeck 40%
                    </p>
                </div>
            </li>
        </ul>
    </div>
</div>


<div class="block block-publication">
    <div class="block-title">
        <span>Other publication list</span>
    </div>
    <div  class="block-content">
        <p>1. Interactively deploy, <span>Client-centered innovation vis-a-vis fully tested mindshare,pg#</span></p>
        <p>2. Underwhelm, <span>Continually grow ,pg#</span></p>
        <p>3. Efficiently conceptualize, <span>Holisticly leverage ,pg#</span></p>
    </div>
</div>

<div class="block block-authorlist">
    <div class="block-title">
        <span>Co-authorlist</span>
    </div>
    <div  class="block-content">
        <ul class="authorlist">
            <li class="">
                <img src="{{url:site}}media/demo/avata1.jpg" class="avata">
                <div class="info">
                    <p class="username"><strong>Micky Ward</strong></p>
                    <p>Forest Sciencetist<br />Author @ Sciencehub.com</p>
                    <p><span>Score:</span> 65798<br /><span>Followers:</span> 268</p>
                </div>
            </li>
            <li class="">
                <img src="{{url:site}}media/demo/avata2.jpg" class="avata">
                <div class="info">
                    <p class="username"><strong>Annie</strong></p>
                    <p>Forest Sciencetist<br />Author @ Sciencehub.com</p>
                    <p><span>Score:</span> 65798<br /><span>Followers:</span> 268</p>
                </div>
            </li>
            <li class="">
                <img src="{{url:site}}media/demo/avata3.jpg" class="avata">
                <div class="info">
                    <p class="username"><strong>Sherry</strong></p>
                    <p>Forest Sciencetist<br />Author @ Sciencehub.com</p>
                    <p><span>Score:</span> 65798<br /><span>Followers:</span> 268</p>
                </div>
            </li>
            <li class="">
                <img src="{{url:site}}media/demo/avata1.jpg" class="avata">
                <div class="info">
                    <p class="username"><strong>Micky Ward</strong></p>
                    <p>Forest Sciencetist<br />Author @ Sciencehub.com</p>
                    <p><span>Score:</span> 65798<br /><span>Followers:</span> 268</p>
                </div>
            </li>
        </ul>
    </div>
</div>

</div>