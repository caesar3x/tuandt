<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/20/13
 */
?>
<div class="container">
    <form id="upload-book" action="#" >
        <div class="upload-book">
            <div class="upload-img">
                <img src="{{url:site}}media/upload.jpg" />
                <p>Upload document thumbnail</p>
                <ul>
                    <li>img name.png <a href="#"><i class="icon-close"></i></a></li>
                </ul>
                <a class="button btn_upload">Upload image</a>
            </div>
        </div>
        <div class="upload-article">
            <div class="wrap-title">
                <h2 class="up-title">Post article</h2>
                <div class="g_button right">
                    <a href="#" class="button btn_publish">Publish</a>
                    <a href="#" class="button btn_preview">preview</a>
                    <a href="#" class="button btn_save">save</a>
                    <a href="#" class="button btn_cancel">cancel</a>
                    <a href="#" class="button btn_share">Share draft with authors</a>
                </div>
            </div>
            <div class="form">
                <div class="main">
                    <label>Main category</label>
                    <select id="category" class="large" name="category">
                        <option>Select category</option>
                        <option>Category 1</option>
                        <option>Category 2</option>
                        <option>Category 3</option>
                        <option>Category 4</option>
                    </select>
                    <label>Sub category</label>
                    <input type="text" name="sub_category" value="" placeholder="Select sub category" class="large" />
                    <label>Document type</label>
                    <input type="text" name="document_type" value="" placeholder="Select document type" class="large" />

                </div>
                <div class="title">
                    <label>Article titlte <em>*</em></label>
                    <textarea placeholder="Add title" rows="3" name="article_titlte" id="article_titlte"></textarea>
                    <label>Sub titlte <em>*</em></label>
                    <textarea placeholder="Add sub title" rows="3" name="sub_titlte" id="sub_titlte"></textarea>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="wrap">
<div class="col-main">
    <div class="wrap-book-des">
        <div class="col-nav">
            <div class="box-nav box-history">
                <div class="box-title"><span>History</span></div>
                <div class="box-content">
                    <ul>
                        <li>
                            <p><strong>Version 1</strong></p>
                            <p><span class="left">10:30 pm - 22/6</span>
                                <a href="#" class="button right">Recall</a>
                            </p>
                        </li>
                        <li>
                            <p><strong>Version 2</strong></p>
                            <p><span class="left">10:30 pm - 22/6</span>
                                <a href="#" class="button right">Recall</a>
                            </p>
                        </li>
                        <li>
                            <p><strong>Version 3</strong></p>
                            <p><span class="left">10:30 pm - 22/6</span>
                                <a href="#" class="button right">Recall</a>
                            </p>
                        </li>
                        <li>
                            <p><strong>Version 4</strong></p>
                            <p><span class="left">10:30 pm - 22/6</span>
                                <a href="#" class="button right">Recall</a>
                            </p>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="box-nav box-referrences">
                <div class="box-title"><span>Referrences</span></div>
                <div class="box-content">
                    <p>1. Interactively deploy, <span>Client-centered innovation vis-a-vis fully tested mindshare,pg#</span></p>
                    <p>2. Underwhelm, <span>Continually grow ,pg#</span></p>
                    <p>3. Efficiently conceptualize, <span>Holisticly leverage ,pg#</span></p>
                    <a href="#" class="button">Add new references</a>
                </div>
            </div>

            <div class="box-nav box-attach">
                <div class="box-title"><span>list of attachments</span></div>
                <div class="box-content download">
                    <ul>
                        <li class="item">
                            <p class="name">1. The Science of the bottom <a href="#"><i class="icon-close"></i></a></p>
                            <p class="des">Progressively iterate accurate strategic theme areas without competitive portals. Energistically communicate accurate partnerships for high </p>
                            <p class="info">Size format: <span>2MB</span> Code: <span>DG843</span><br /> File format: <a href="#"><i class="icon-msdoc"></i></a></p>
                        </li>
                        <li class="item">
                            <p class="name">2. The universe in a nutshell <a href="#"><i class="icon-close"></i></a></p>
                            <p class="des">Progressively iterate accurate strategic theme areas without competitive portals. Energistically communicate accurate partnerships for high </p>
                            <p class="info">Size format: <span>2MB</span> Code: <span>DG843</span><br /> File format: <a href="#"><i class="icon-msppt"></i></a></p>
                        </li>
                        <li class="item">
                            <p class="name">3. Stay hungry, stay foolish <a href="#"><i class="icon-close"></i></a></p>
                            <p class="des">Progressively iterate accurate strategic theme areas without competitive portals. Energistically communicate accurate partnerships for high </p>
                            <p class="info">Size format: <span>2MB</span> Code: <span>DG843</span><br /> File format: <a href="#"><i class="icon-msppt"></i></a></p>
                        </li>
                        <a href="#" class="button">Add attachments</a>
                    </ul>
                </div>
            </div>
        </div>
        <div class="author-list">
            <div class="block block-author-list">
                <div class="block-title">
                    <span>Fill author list</span>
                </div>
                <div class="block-content">
                    <p class="radio"><input type="radio" name="authors" value="is" />The authors is already a ScienceRevolution user</p>
                    <p class="radio checked"><input type="radio" name="authors" checked="checked" value="isnot" />The authors is not yet a ScienceRevolution user</p>
                    <p class="text">
                        <input type="text" name="author_name" value="" placeholder="Author name" class="left" />
                        <input type="text" name="author_email" value="" placeholder="Author email" class="right" />
                        <input type="text" name="author_sur_name" value="" placeholder="Author sur name" class="left" />
                        <input type="text" name="author_qualification" value="" placeholder="Author  qualification" class="right" />
                    </p>
                    <p>The task what this author has involved</p>
                    <textarea placeholder="Task description" rows="2" name="task_description" id="task_des"></textarea>
                    <div class="addauthor">
                        <span class="left percentage">The percentage of the contribution</span>
                        <input class="large left" name="percentage" />
                        <span class="left unit">%</span>

                        <a href="#" class="right button large btn_addauthor">Add author</a>
                    </div>

                    <div class="block-document">
                        <table>
                            <tr class="head">
                                <td class="author">Author</td>
                                <td class="task">Task involved</td>
                                <td class="percent" >Percent</td>
                            </tr>
                            <tr>
                                <td class="author"><span class="title">Dr Sebastian</span>     <span>|<span class="value">2.15K</span> Score </span>    <span>|<span class="value">549</span> Follower</span><br/>
                                    Code: SEB442 - Forest Scientist - MIT</td>
                                <td class="task">Writer</td>
                                <td class="percent">50%</td>
                            </tr>
                            <tr>
                                <td class="author"><span class="title">Andree Sulivan</span>     <span>|<span class="value">2.15K</span> Score </span>    <span>|<span class="value">549</span> Follower</span><br/>
                                    Code: SEB442 - Forest Scientist - MIT</td>
                                <td class="task">Editor</td>
                                <td class="percent">30%</td>
                            </tr>
                            <tr>
                                <td class="author"><span class="title">Eru Roraito</span>     <span>|<span class="value">2.15K</span> Score </span>    <span>|<span class="value">549</span> Follower</span><br/>
                                    Code: SEB442 - Forest Scientist - MIT</td>
                                <td class="task">Qualiti checker</td>
                                <td class="percent">25%</td>
                            </tr>
                            <tr class="footer">
                                <td class="author"></td>
                                <td class="task"></td>
                                <td class="percent" >Total : 125%</td>
                            </tr>
                        </table>
                    </div>

                    <div class="adddoc">
                        <label>Abtrast <em>*</em></label>
                        <textarea placeholder="Add Author Background" rows="3" name="abtrast"></textarea>

                        <label>Background <em>*</em></label>
                        <textarea placeholder="Add Background" rows="3" name="background"></textarea>

                        <label>Software with Tool & Instruments used</label>
                        <textarea placeholder="Add Software with Tool & Instruments used" rows="3" name="used"></textarea>

                        <label>Material used</label>
                        <textarea placeholder="Add Material used" rows="3" name="material"></textarea>

                        <label>Section title </label>
                        <input placeholder="Add Section title"  name="section_title"/>

                        <label>Section content <em>*</em></label>
                        <textarea placeholder="Section content" rows="3" name="section"></textarea>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>
<div class="col-right bg1">
    <div class="box-nav box-ask">
        <div class="box-title"><span>Ask for a peer view</span></div>
        <div class="box-content">
            <form action="#" id="ask-for-peer-view">
                <p><input type="text" name="ask-name"   placeholder="Enter user name" id="ask-name" />
                    <button class="button btn_ok"><span>ok</span></button>	</p>
                <p class="note">Insert reviewer detail and Science to contact him for you</p>
                <p><input type="text" name="reviewer-name"   placeholder="Reviewer sur name" id="reviewer-name" /></p>
                <p><input type="text" name="reviewer-sur-name"   placeholder="Reviewer sur name" id="reviewer-sur-name" /></p>
                <p><input type="text" name="reviewer-email"   placeholder="Reviewer email" id="reviewer-email" /></p>
                <p class="button_ask"><button class="button right btn_ask"><span>ask</span></button></p>
                <p>
                    <select class=" " name="ask-comment" id="ask-comment">
                        <option>Select a comment</option>
                        <option>Comment 1</option>
                        <option>Comment 2</option>
                        <option>Comment 3</option>
                    </select>
                </p>
                <p class="add-share">
                    <a href="#" class="button">Add comment</a>
                    <a href="#" class="button">Share comment</a>
                </p>
                <p>
                    <label>Comment for Authorlist</label>
                    <textarea id="c_authorlist" name="c_authorlist" rows="4" placeholder="Add comments" ></textarea>
                </p>
                <p>
                    <label>Comment for Abstract</label>
                    <textarea id="c_abstract" name="c_abstract" rows="4" placeholder="Add comments" ></textarea>
                </p>
                <p>
                    <label>Comment for  Background</label>
                    <textarea id="c_background" name="c_background" rows="4" placeholder="Add comments" ></textarea>
                </p>
                <p>
                    <label>Comment for  Software with Tool & Instruments used</label>
                    <textarea id="c_used" name="c_used" rows="4" placeholder="Add comments" ></textarea>
                </p>
                <p>
                    <label>Comment for Material used</label>
                    <textarea id="c_material" name="c_material" rows="4" placeholder="Add comments" ></textarea>
                </p>
                <p>
                    <label>Comment for Section</label>
                    <textarea id="c_section" name="c_section" rows="4" placeholder="Add comments" ></textarea>
                </p>

            </form>
        </div>
    </div>

</div>
</div>