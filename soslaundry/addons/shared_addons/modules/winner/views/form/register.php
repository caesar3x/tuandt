<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/4/13
 */
?>
<header>
    <h2>Register Information</h2>
</header>
<form action="#">

    <div><label class="desc" for="Field1" id="title1">Full Name</label>

        <div><input class="field text fn" id="Field1" name="Field1" size="8" tabindex="1" type="text" value="" /></div>
    </div>

    <div>
        <label class="desc" for="Field3" id="title3">Email </label>
        <div><input id="Field3" maxlength="255" name="Field3" spellcheck="false" tabindex="3" type="email" value="" /></div>
    </div>
    <div>
        <label class="desc" for="Field9" id="title9">Phone </label>
        <div><input id="Field9" maxlength="255" name="Field3" spellcheck="false" tabindex="3" type="email" value="" /></div>
    </div>
    <div>
        <label class="desc" for="Field106" id="title106">Choose the hotel you're staying at</label>

        <div><select class="field select medium" id="Field106" name="Field106" tabindex="11"><option value="First Choice">First Choice</option><option value="Second Choice">Second Choice</option><option value="Third Choice">Third Choice</option> </select></div>
    </div>

    <div>
        <div><input id="saveForm" name="saveForm" type="submit" value="Submit" /></div>
    </div>
</form>
