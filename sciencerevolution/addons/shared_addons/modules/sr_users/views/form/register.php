<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/13/13
 */
?>
<div class="container">
    <h2 class="page-title"><span>Sign up</span></h2>
    <div class="row-fluid">
        <div class="col-main span9">
            <div class="page-content">
                <div class="row-fluid">
                    <div class="span12">
                        <form id="signup-form" method="post" action="{{ url:current}}">
                            <label>Name</label>
                            <input class="large" type="text" class="input-text"  placeholder="Fisrt & Last" value="" name="name" id="name" />
                            <label>Phone number</label>
                            <input class="large" type="text" class="input-text" placeholder="(000) 123 - 859" value="" name="phone_number"  id="phone_number" />
                            <label>Email address</label>
                            <input class="large" type="text" class="input-text" placeholder="User@domain.com" value="" name="email"  id="email" />
                            <label>Website</label>
                            <input class="large" type="text" class="input-text" placeholder="www.domainname.com" value="" name="website"  id="website" />
                            <label>Reason</label>

                            <select class="large" name="reason" id="reason">
                                <option select="selected">Please select your reason</option>
                                <option>Reason 1</option>
                                <option>Reason 2</option>
                            </select>
                            <label>Message</label>
                            <textarea id="message" name="message" rows="4" placeholder="Any additional information about your location or inquiry"></textarea>
                            <button class="button large right" title="Search" type="submit"><span>SUBMIT</span></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-right span3">
            <div class="block block-quicklinks">
                <ul>
                    <li class="first"><a href="#"><i class="icon-letter"></i> Contact & support</a></li>
                    <li class=""><a href="#"><i class="icon-info-sign"></i> Information & advertiser</a></li>
                    <li class=""><a href="#"><i class="icon-book"></i> Terms & conditions</a></li>
                    <li class=""><a href="#"><i class="icon-lock"></i> Privacy policy</a></li>
                    <li class=""><a href="#"><i class="icon-question-sign"></i> How it work</a></li>
                    <li class="last"><a href="#"><i class="icon-pencil"></i> FAQ</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>