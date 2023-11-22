<?php
/*
	Template Name: Article Form Page
*/

ini_set('display_errors',1);
error_reporting(E_ALL);

	get_header();

    $file = __DIR__ . '/../../uploads/time_record.txt';
    $path = __DIR__ . '/../../uploads/wpallimport/files/generated-post.xml';

    $title = '';
    $h1title = '';
    $meta_title = '';
    $url = '';
    $url_descr = '';
    $anchor = '';
    $post_url = '';
    $file_url = '';
    $youtubeUrl = '';
    $faq_theme = '';

    if(file_exists($path)) {
        $xmlstring = file_get_contents($path);
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $arrayArticles = json_decode($json, TRUE);

        if(count($arrayArticles) > 0) {
            if(count($arrayArticles) == 1) {
                $title = $arrayArticles["page"]["title"];
                $h1title = $arrayArticles["page"]["h1title"];
                $meta_title = $arrayArticles["page"]["page_meta"];
                $url = $arrayArticles["page"]["url"];
                $url_descr = $arrayArticles["page"]["url_descr"];
                $anchor = $arrayArticles["page"]["anchor"];
                $post_url = $arrayArticles["page"]["post_url"];
                $file_url = $arrayArticles["page"]["page_image"];
                $youtubeUrl = $arrayArticles["page"]["youtube_url"];
                $apps_links = $arrayArticles["page"]["apps_links"];
                $faq_theme = $arrayArticles["page"]["faq_theme"];
            } 
            
            if(count($arrayArticles) > 1) {
                $title = $arrayArticles["page"][count($arrayArticles) - 1]["title"];
                $h1title = $arrayArticles["page"][count($arrayArticles) - 1]["h1title"];
                $meta_title = $arrayArticles["page"][count($arrayArticles) - 1]["page_meta"];
                $url = $arrayArticles["page"][count($arrayArticles) - 1]["url"];
                $url_descr = $arrayArticles["page"][count($arrayArticles) - 1]["url_descr"];
                $anchor = $arrayArticles["page"][count($arrayArticles) - 1]["anchor"];
                $post_url = $arrayArticles["page"][count($arrayArticles) - 1]["post_url"];
                $file_url = $arrayArticles["page"][count($arrayArticles) - 1]["page_image"];
                $youtubeUrl = $arrayArticles["page"][count($arrayArticles) - 1]["youtube_url"];
                $apps_links = $arrayArticles["page"][count($arrayArticles) - 1]["apps_links"];
                $faq_theme = $arrayArticles["page"][count($arrayArticles) - 1]["faq_theme"];
            }

            
        }
    }

    $current = (int)file_get_contents($file);
	
?>
<style>
    main {
        padding-top: 0 !important;
    }
    .container {
        position: relative;
        max-width: 100% !important;
        padding: 0 24px !important;
        display: flex;
        justify-content: space-between;
        gap: 100px;
    }
    form {
        max-width: 600px;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    label, input {
        display: block;
        width: 100%;
    }
    input {
        margin: 10px 0 20px;
        border: 2px solid #333333;
        border-radius: 12px;
        padding: 6px 12px;
        font-size: 16px;
        line-height: 24px;
    }
    .sBtn {
        background: #333 !important;
        color: white;
        display: inline-block;
        max-width: 320px;
        margin: 60px 0 0;
        width: 100%;
        border-radius: 12px;
        font-size: 16px;
        line-height: 24px;
        padding: 6px 12px;
    }

    button:disabled {
        opacity: 0.5;
    }

    h1 {
        text-align: center;
    }
    .hidden {
        display: none;
    }
    .img {
        display: block;
        max-width: 400px;
        height: auto;
        margin-bottom: 40px;
    }
    .modal,
    .loader {
        display: none;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100vh;
        background-color: rgb(173 173 173 / 80%);
    }
    .loader img {
        width: 100px;
        height: 100px;
    }
    .modal.show,
    .loader.show {
        display: flex;
    }
    #moreFAq {
        margin-top: 65px;
        text-align: center;
        font-weight: bold;
    }
    .checkbox {
        display:flex;
        align-items: center;
        font-weight: bold;
    }
    .checkbox input {
        width: 20px;
        height: 20px;
        margin: 0 12px 0 0;
    }
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
        margin-left: -24px;
    }
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        width: 100%;
        font-size: 18px;
    }
    .tab button:hover {
        background-color: #ddd;
    }
    .tab button.active {
        background-color: #ccc;
    }
    .tabcontent {
        display: none;
        flex: 1 0;
        padding: 30px 0 0;
        border-top: none;
        animation: fadeEffect 1s;
    }
    .generate {
        color: green;
    }
    br {
        display: block !important;
    }
    .dropbtn {
        background-color: #04AA6D;
        background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIHdpZHRoPSIyNTYiIGhlaWdodD0iMjU2IiB2aWV3Qm94PSIwIDAgMjU2IDI1NiIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+Cgo8ZGVmcz4KPC9kZWZzPgo8ZyBzdHlsZT0ic3Ryb2tlOiBub25lOyBzdHJva2Utd2lkdGg6IDA7IHN0cm9rZS1kYXNoYXJyYXk6IG5vbmU7IHN0cm9rZS1saW5lY2FwOiBidXR0OyBzdHJva2UtbGluZWpvaW46IG1pdGVyOyBzdHJva2UtbWl0ZXJsaW1pdDogMTA7IGZpbGw6IG5vbmU7IGZpbGwtcnVsZTogbm9uemVybzsgb3BhY2l0eTogMTsiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEuNDA2NTkzNDA2NTkzNDAxNiAxLjQwNjU5MzQwNjU5MzQwMTYpIHNjYWxlKDIuODEgMi44MSkiID4KCTxwYXRoIGQ9Ik0gOTAgMjQuMjUgYyAwIC0wLjg5NiAtMC4zNDIgLTEuNzkyIC0xLjAyNSAtMi40NzUgYyAtMS4zNjYgLTEuMzY3IC0zLjU4MyAtMS4zNjcgLTQuOTQ5IDAgTCA0NSA2MC44IEwgNS45NzUgMjEuNzc1IGMgLTEuMzY3IC0xLjM2NyAtMy41ODMgLTEuMzY3IC00Ljk1IDAgYyAtMS4zNjYgMS4zNjcgLTEuMzY2IDMuNTgzIDAgNC45NSBsIDQxLjUgNDEuNSBjIDEuMzY2IDEuMzY3IDMuNTgzIDEuMzY3IDQuOTQ5IDAgbCA0MS41IC00MS41IEMgODkuNjU4IDI2LjA0MiA5MCAyNS4xNDYgOTAgMjQuMjUgeiIgc3R5bGU9InN0cm9rZTogbm9uZTsgc3Ryb2tlLXdpZHRoOiAxOyBzdHJva2UtZGFzaGFycmF5OiBub25lOyBzdHJva2UtbGluZWNhcDogYnV0dDsgc3Ryb2tlLWxpbmVqb2luOiBtaXRlcjsgc3Ryb2tlLW1pdGVybGltaXQ6IDEwOyBmaWxsOiByZ2IoMjU1LDI1NSwyNTUpOyBmaWxsLXJ1bGU6IG5vbnplcm87IG9wYWNpdHk6IDE7IiB0cmFuc2Zvcm09IiBtYXRyaXgoMSAwIDAgMSAwIDApICIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiAvPgo8L2c+Cjwvc3ZnPg==");
        background-position: center right 6px;
        background-size: 14px 10px;
        background-repeat: no-repeat;
        color: white;
        padding: 16px 32px 16px 16px;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }
    svg{
        width: 100px;
        height: 100px;
        margin: 20px auto 0;
        display:inline-block;
    }
    .dropbtn:hover, .dropbtn:focus {
        background-color: #3e8e41;
    }
    #myInput {
        box-sizing: border-box;
        font-size: 16px;
        padding: 14px 20px 12px;
        border: none;
        border-bottom: 1px solid #ddd;
        margin: 10px;
        max-width: calc(100% - 20px);
    }
    #myInput:focus {outline: 3px solid #ddd;}
    .dropdown {
        position: relative;
        display: inline-block;
        margin-bottom: 30px;
    }
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f6f6f6;
        min-width: 400px;
        max-width: 400px;
        border: 1px solid #ddd;
        z-index: 1;
        border-radius: 10px;
        max-height: 400px;
        overflow-y: auto;
    }
    .dropdown-content .option {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        cursor: pointer;
    }
    .dropdown-content .option:hover {background-color: #f1f1f1}
    .show {display:block;}
    .danger {
        background: red !important;
    }
    .modal-w {
        background: #FFF;
        padding: 40px;
        border-radius: 16px;
        width: 600px;
    }
    .modal-w button {
        background: #333;
        color: #fff;
        display: inline-block;
        text-align: center;
        padding: 12px;
        width: 47%;
        margin-right: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }
    .modal-w button:last-child {
        background: red !important;
        margin-right: 0;
    }
    @keyframes fadeEffect {
        from {opacity: 0;}
        to {opacity: 1;}
    }
</style>
		<main>
			<div class="container">
                <div class="loader">
                    <h1>AI is working (2-4 mins to finish,<br> поки погодуй кота чи собаку ;-)</h1>
                    <svg version="1.1" id="L1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                        <circle fill="none" stroke="#fff" stroke-width="6" stroke-miterlimit="15" stroke-dasharray="14.2472,14.2472" cx="50" cy="50" r="47" >
                            <animateTransform 
                                attributeName="transform" 
                                attributeType="XML" 
                                type="rotate"
                                dur="5s" 
                                from="0 50 50"
                                to="360 50 50" 
                                repeatCount="indefinite" />
                        </circle>
                        <circle fill="none" stroke="#fff" stroke-width="1" stroke-miterlimit="10" stroke-dasharray="10,10" cx="50" cy="50" r="39">
                            <animateTransform 
                                attributeName="transform" 
                                attributeType="XML" 
                                type="rotate"
                                dur="5s" 
                                from="0 50 50"
                                to="-360 50 50" 
                                repeatCount="indefinite" />
                        </circle>
                        <g fill="#fff">
                            <rect x="30" y="35" width="5" height="30">
                                <animateTransform 
                                attributeName="transform" 
                                dur="1s" 
                                type="translate" 
                                values="0 5 ; 0 -5; 0 5" 
                                repeatCount="indefinite" 
                                begin="0.1"/>
                            </rect>
                            <rect x="40" y="35" width="5" height="30" >
                                <animateTransform 
                                attributeName="transform" 
                                dur="1s" 
                                type="translate" 
                                values="0 5 ; 0 -5; 0 5" 
                                repeatCount="indefinite" 
                                begin="0.2"/>
                            </rect>
                            <rect x="50" y="35" width="5" height="30" >
                                <animateTransform 
                                attributeName="transform" 
                                dur="1s" 
                                type="translate" 
                                values="0 5 ; 0 -5; 0 5" 
                                repeatCount="indefinite" 
                                begin="0.3"/>
                            </rect>
                            <rect x="60" y="35" width="5" height="30" >
                                <animateTransform 
                                attributeName="transform" 
                                dur="1s" 
                                type="translate" 
                                values="0 5 ; 0 -5; 0 5"  
                                repeatCount="indefinite" 
                                begin="0.4"/>
                            </rect>
                            <rect x="70" y="35" width="5" height="30" >
                                <animateTransform 
                                attributeName="transform" 
                                dur="1s" 
                                type="translate" 
                                values="0 5 ; 0 -5; 0 5" 
                                repeatCount="indefinite" 
                                begin="0.5"/>
                            </rect>
                        </g>
                    </svg>
                </div>
                <?php if(time() > $current) { ?>
                    <div class="tab">
                        <button class="tablinks" onclick="openTab(event, 'createdArticles')" id="defaultOpen">Generated Articles</button>
                        <button class="tablinks generate" onclick="openTab(event, 'generateArticle')" id="genNewArt">+ Generate Article</button>
                    </div>
                    <div id="createdArticles" class="tabcontent">
                        <div>
                            <?php if(count($arrayArticles) > 1) { ?>
                                <div class="dropdown">
                                    <button onclick="openDropdown()" class="dropbtn">Select an article to edit</button>
                                    <div id="myDropdown" class="dropdown-content">
                                        <input type="text" placeholder="Search title article.." id="myInput" onkeyup="filterFunction()">
                                        <?php for($i = 0; $i < count($arrayArticles); $i++ ) { ?>
                                            <div
                                                class="option"
                                                data-title="<?php echo $arrayArticles["page"][$i]["title"]; ?>"
                                                data-h1title="<?php echo $arrayArticles["page"][$i]["h1title"]; ?>"
                                                data-meta_title="<?php echo $arrayArticles["page"][$i]["page_meta"]; ?>"
                                                data-url="<?php echo $arrayArticles["page"][$i]["url"]; ?>"
                                                data-url_descr="<?php echo $arrayArticles["page"][$i]["url_descr"]; ?>"
                                                data-anchor="<?php echo $arrayArticles["page"][$i]["anchor"]; ?>"
                                                data-post_url="<?php echo $arrayArticles["page"][$i]["post_url"]; ?>"
                                                data-file_url="<?php echo $arrayArticles["page"][$i]["page_image"]; ?>"
                                                data-youtubeUrl="<?php echo $arrayArticles["page"][$i]["youtube_url"]; ?>"
                                                data-apps_links="<?php echo $arrayArticles["page"][$i]["apps_links"]; ?>"
                                                data-faq_theme="<?php echo $arrayArticles["page"][$i]["faq_theme"]; ?>"
                                            >
                                                <?php echo $arrayArticles["page"][$i]["title"]; ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if(count($arrayArticles) > 0) { ?>
                                <h3>Last / Selected article:</h3>
                                <p id="lastTitle">TITLE: <span><?php echo $title;?></span></p>
                                <p id="lastH1">H1 TITLE: <span><?php echo $h1title;?></span></p>
                                <p id="lastPostUrl">URL for Post: <a target="_blank" href="<?php echo home_url() . '/' . $post_url . '/'; ?>"><?php echo home_url() . '/' . $post_url . '/';?></a></p>
                                <p id="lastMeta">META TITLE: <span><?php echo $meta_title;?></span></p>
                                <p id="lastURL">URL: <span><?php echo $url;?></span></p>
                                <p id="lastURLDescr">URL Description: <span><?php echo $url_descr;?></span></p>
                                <p id="lastAnchor">Anchor: <span><?php echo $anchor;?></span></p>
                                <img id="lastIMG" src="<?php echo home_url() . '/wp-content' . explode('wp-content', $file_url)[1];?>" alt="img" class="img">
                                <button type="button" class="sBtn" id="btn-reg">REGENERATE</button>
                                <button type="button" class="sBtn danger" id="btn-remove">REMOVE ARTICLE</button>

                                <form id="faqQuestions" action="/" data-action="<?php echo home_url() . '/wp-content/uploads/faq-script.php'; ?>">
                                    <label for="btn-num-faq" id="moreFAq">ADD MORE FAQ QUESTIONS (default + 10)</label>
                                    <input type="number" id="numberFaq" name="numberFaq" min="1" max="30" placeholder="Quantity questions (number only)">
                                    <input class="hidden" type="text" id="faqLastTheme" value="<?php echo $faq_theme; ?>" name="themeFaq">
                                    <input class="hidden" type="text" id="removeArticle" value="false" name="remove_article">
                                    <button  class="sBtn" type="button" id="btn-num-faq">ADD MORE QUESTIONS</button>
                                </form>

                                <form id="formRemoveArt" action="/" data-action="<?php echo home_url() . '/wp-content/uploads/remove-script.php'; ?>">
                                    <input class="hidden" type="text" id="removeTitle" name="remove_title" value="<?php echo $title;?>">
                                </form>
                            <?php } else { ?>
                                <h3>There are no articles generated. Let's start!</h3>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="generateArticle" class="tabcontent">
                        <form id="article" action="/" data-action="<?php echo home_url() . '/wp-content/uploads/article-script.php'; ?>">
                            <h3>New record:</h3>
                            <label for="title">What would you like to write about (max 150 characters)</label>
                            <input type="text" id="title" name="title" maxlength="150" data-last="<?php echo $title;?>">
                            <label for="h1title">H1 (Article Title) (max 150 characters)</label>
                            <input type="text" id="h1title" name="h1title" maxlength="150" data-last="<?php echo $h1title;?>">
                            <label for="post_url">URL (/folder/url/)</label>
                            <input type="text" id="post_url" placeholder="slug" name="post_url" data-last="<?php echo $post_url;?>">
                            <label for="title">META TITLE</label>
                            <input type="text" id="meta_title" name="meta_title" data-last="<?php echo $meta_title;?>">
                            <label for="url">URL to pass link juice (Dofollow)</label>
                            <input type="text" id="url" name="url" data-last="<?php echo $url;?>">
                            <label for="url_descr">What does this link lead to, and where will users be directed if they click on it? (max 150 characters)</label>
                            <input type="text" id="url_descr" name="url_descr" maxlength="150" data-last="<?php echo $url_descr;?>">
                            <label for="anchor">Link Anchor (ex: “how to do something” Don’t spam!)</label>
                            <input type="text" id="anchor" name="anchor" data-last="<?php echo $anchor;?>">
                            <label for="file">Featured Image (JPG only)</label>
                            <input type="file" name="file" id="file">
                            <input type="text" name="file_url" id="file_url" class="hidden" data-last="<?php echo $file_url;?>">
                            <input type="text" name="domain_url" id="domain_url" class="hidden" value="<?php echo home_url(); ?>">
                            <label for="faq_theme">Please provide me with either a specific niche or a top keyword for which you would like an FAQ generated</label>
                            <input type="text" id="faq_theme" name="faq_theme" data-last="<?php echo $faq_theme;?>">
                            <label for="youtube_url">Add Youtube Link</label>
                            <input type="text" id="youtube_url" name="youtube_url" data-last="<?php echo $youtubeUrl;?>">
                            <label for="apps_links" class="checkbox">
                                <input type="checkbox" name="apps_links" id="apps_links" data-checked="<?php echo $apps_links; ?>">
                                Click here if you want your article to look like a “list of/best of” style (not a how to style)
                            </label>
                            <button  class="sBtn" type="submit" id="btn">Generate Article</button>
                        </form>
                    </div>
                <?php } else { ?>
                    <div class="loader show">
                        <h1>Article import...please wait,<br> autoreload will happen in 1-2 minutes,<br> усі нагодовані :-)?</h1>
                        <svg version="1.1" id="L1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                            <circle fill="none" stroke="#fff" stroke-width="6" stroke-miterlimit="15" stroke-dasharray="14.2472,14.2472" cx="50" cy="50" r="47" >
                                <animateTransform 
                                    attributeName="transform" 
                                    attributeType="XML" 
                                    type="rotate"
                                    dur="5s" 
                                    from="0 50 50"
                                    to="360 50 50" 
                                    repeatCount="indefinite" />
                            </circle>
                            <circle fill="none" stroke="#fff" stroke-width="1" stroke-miterlimit="10" stroke-dasharray="10,10" cx="50" cy="50" r="39">
                                <animateTransform 
                                    attributeName="transform" 
                                    attributeType="XML" 
                                    type="rotate"
                                    dur="5s" 
                                    from="0 50 50"
                                    to="-360 50 50" 
                                    repeatCount="indefinite" />
                            </circle>
                            <g fill="#fff">
                                <rect x="30" y="35" width="5" height="30">
                                    <animateTransform 
                                    attributeName="transform" 
                                    dur="1s" 
                                    type="translate" 
                                    values="0 5 ; 0 -5; 0 5" 
                                    repeatCount="indefinite" 
                                    begin="0.1"/>
                                </rect>
                                <rect x="40" y="35" width="5" height="30" >
                                    <animateTransform 
                                    attributeName="transform" 
                                    dur="1s" 
                                    type="translate" 
                                    values="0 5 ; 0 -5; 0 5" 
                                    repeatCount="indefinite" 
                                    begin="0.2"/>
                                </rect>
                                <rect x="50" y="35" width="5" height="30" >
                                    <animateTransform 
                                    attributeName="transform" 
                                    dur="1s" 
                                    type="translate" 
                                    values="0 5 ; 0 -5; 0 5" 
                                    repeatCount="indefinite" 
                                    begin="0.3"/>
                                </rect>
                                <rect x="60" y="35" width="5" height="30" >
                                    <animateTransform 
                                    attributeName="transform" 
                                    dur="1s" 
                                    type="translate" 
                                    values="0 5 ; 0 -5; 0 5"  
                                    repeatCount="indefinite" 
                                    begin="0.4"/>
                                </rect>
                                <rect x="70" y="35" width="5" height="30" >
                                    <animateTransform 
                                    attributeName="transform" 
                                    dur="1s" 
                                    type="translate" 
                                    values="0 5 ; 0 -5; 0 5" 
                                    repeatCount="indefinite" 
                                    begin="0.5"/>
                                </rect>
                            </g>
                        </svg>
                    </div>
                    <script>
                        setTimeout(function(){
                            let fullUrl = window.location.href
                            const needUrl = fullUrl.split('?')
                            window.location = needUrl[0]+'?eraseCache=' + Math.floor(Math.random() * 1000000000)
                        }, 120000);
                    </script>
                <?php } ?>
			</div>
		</main>
        <script>
        jQuery(document).ready(function() {
            jQuery('#btn-remove').on('click', function(e) {
                e.preventDefault()

                jQuery('.modal').addClass('show')
            })
            jQuery('#cancelRemove').on('click', function(e) {
                e.preventDefault()

                jQuery('.modal').removeClass('show')
            })
            jQuery('#btn-num-faq').on('click', function(e) {
                e.preventDefault()

                if(!jQuery('#numberFaq')[0].value) {
                    jQuery('#numberFaq')[0].value = 10
                }

                jQuery("#faqQuestions").submit()
                
            })
            jQuery("#faqQuestions").on("submit", function(event) {
                event.preventDefault()
                const formData = new FormData(this);

                if( jQuery('#numberFaq')[0].value.trim().length === 0 ) {
                        alert('All fields is required (Faq questions) !!') 
                } else {
                    jQuery('#btn-num-faq').attr('disabled','true');
                    jQuery('.loader').addClass('show');
                    jQuery.ajax({
                        type: 'POST',
                        url: jQuery("#faqQuestions").attr('data-action'),
                        data: formData,
                        success: function(data) {
                            if(data == 'false') {
                                alert('Some error occured in API. Please resend request')
                                jQuery('#btn').prop("disabled", false)
                                jQuery('.loader').removeClass('show')
                                return
                            }

                            alert('Faq updated and imported. Refresh page')
                        },
                        error: function(jqXHR, exception) {
                            setTimeout(function () {
                                location.reload()
                            }, 20000);
                        },
                        cache: false,
                        contentType: false,
                        processData: false,
                        timeout: 120000
                    });
                }
            });
            jQuery('#btn-reg').on('click', function(e) {
                e.preventDefault()
                jQuery('#title')[0].value = jQuery(jQuery('#title')[0]).attr('data-last')
                jQuery('#h1title')[0].value = jQuery(jQuery('#h1title')[0]).attr('data-last')
                jQuery('#meta_title')[0].value = jQuery(jQuery('#meta_title')[0]).attr('data-last')
                jQuery('#url')[0].value = jQuery(jQuery('#url')[0]).attr('data-last')
                jQuery('#anchor')[0].value = jQuery(jQuery('#anchor')[0]).attr('data-last')
                jQuery('#url_descr')[0].value = jQuery(jQuery('#url_descr')[0]).attr('data-last')
                jQuery('#post_url')[0].value = jQuery(jQuery('#post_url')[0]).attr('data-last')
                jQuery('#file_url')[0].value = jQuery(jQuery('#file_url')[0]).attr('data-last')
                jQuery('#youtube_url')[0].value = jQuery(jQuery('#youtube_url')[0]).attr('data-last')
                jQuery('#faq_theme')[0].value = jQuery(jQuery('#faq_theme')[0]).attr('data-last')
                if(jQuery(jQuery('#apps_links')[0]).attr('data-checked') == 'true') {
                    jQuery('#apps_links').prop('checked',true);
                } else {
                    jQuery('#apps_links').prop('checked',false);
                }
                document.getElementById("genNewArt").click();
                jQuery("#article").submit()
            })
            jQuery('#DeleteArticle').on('click', function(e) {
                e.preventDefault()
                jQuery('#formRemoveArt').submit()
            })
            jQuery('#formRemoveArt').on("submit", function(event) {
                event.preventDefault()
                const formData = new FormData(this);
                jQuery.ajax({
                    type: 'POST',
                    url: jQuery("#formRemoveArt").attr('data-action'),
                    data: formData,
                    success: function(data) {
                        if(data == 'false') {
                            alert('Not provided article title')
                            jQuery('.loader').removeClass('show')
                            return
                        }

                        alert('Article removed!')
                        setTimeout(function () {
                            location.reload()
                        }, 3000);
                    },
                    error: function(jqXHR, exception) {
                        setTimeout(function () {
                            location.reload()
                        }, 20000);
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                    timeout: 120000
                });
            })
            jQuery("#article").on("submit", function(event) {
                event.preventDefault()
                const formData = new FormData(this);
                
                if( jQuery('#title')[0].value.trim().length === 0 ||
                    jQuery('#h1title')[0].value.trim().length === 0 ||
                    jQuery('#meta_title')[0].value.trim().length === 0 ||
                    jQuery('#url')[0].value.trim().length === 0 ||
                    jQuery('#anchor')[0].value.trim().length === 0 ||
                    (jQuery('#file')[0].files.length === 0 && jQuery('#file_url')[0].value.trim().length === 0) ||
                    jQuery('#url_descr')[0].value.trim().length === 0 ||
                    jQuery('#post_url')[0].value.trim().length === 0) {
                    alert('All fields is required (Create new article) !!')
                } else {
                    jQuery('#btn').attr('disabled','true');
                    jQuery('.loader').addClass('show');
                    jQuery.ajax({
                        type: 'POST',
                        url: jQuery("#article").attr('data-action'),
                        data: formData,
                        success: function(data) {
                            if(data == 'false') {
                                alert('Some error occured in API. Please resend request')
                                jQuery('#btn').prop("disabled", false)
                                jQuery('.loader').removeClass('show')
                                return
                            }

                            alert('Article imported. Refresh page')
                        },
                        error: function(jqXHR, exception) {
                            setTimeout(function () {
                                location.reload()
                            }, 20000);
                        },
                        cache: false,
                        contentType: false,
                        processData: false,
                        timeout: 120000
                    });
                }
            });
            jQuery(".option").on('click', function(e) {
                document.getElementById("myDropdown").classList.toggle("show");
                jQuery('#lastTitle').find('span').html(jQuery(this).attr('data-title'))
                jQuery('#lastH1').find('span').html(jQuery(this).attr('data-h1title'))
                jQuery('#lastMeta').find('span').html(jQuery(this).attr('data-meta_title'))
                jQuery('#lastURL').find('span').html(jQuery(this).attr('data-url'))
                jQuery('#lastURLDescr').find('span').html(jQuery(this).attr('data-url_descr'))
                jQuery('#lastAnchor').find('span').html(jQuery(this).attr('data-anchor'))
                jQuery('#lastPostUrl').find('a').html(jQuery('#domain_url')[0].value + '/' + jQuery(this).attr('data-post_url') + '/')
                jQuery('#lastPostUrl').find('a').attr('href', jQuery('#domain_url')[0].value + '/' + jQuery(this).attr('data-post_url') + '/')
                jQuery('#lastIMG').attr('src', jQuery('#domain_url')[0].value + '/wp-content' + jQuery(this).attr('data-file_url').split('wp-content')[1])
                jQuery('#lastIMG').attr('srcset', jQuery('#domain_url')[0].value + '/wp-content' + jQuery(this).attr('data-file_url').split('wp-content')[1])
                jQuery('#lastIMG').attr('data-srcset', jQuery('#domain_url')[0].value + '/wp-content' + jQuery(this).attr('data-file_url').split('wp-content')[1])
                jQuery('#lastIMG').attr('data-original', jQuery('#domain_url')[0].value + '/wp-content' + jQuery(this).attr('data-file_url').split('wp-content')[1])

                jQuery(jQuery('#title')[0]).attr('data-last', jQuery(this).attr('data-title'))
                jQuery(jQuery('#h1title')[0]).attr('data-last', jQuery(this).attr('data-h1title'))
                jQuery(jQuery('#meta_title')[0]).attr('data-last', jQuery(this).attr('data-meta_title'))
                jQuery(jQuery('#url')[0]).attr('data-last', jQuery(this).attr('data-url'))
                jQuery(jQuery('#anchor')[0]).attr('data-last', jQuery(this).attr('data-anchor'))
                jQuery(jQuery('#url_descr')[0]).attr('data-last', jQuery(this).attr('data-url_descr'))
                jQuery(jQuery('#post_url')[0]).attr('data-last', jQuery(this).attr('data-post_url'))
                jQuery(jQuery('#file_url')[0]).attr('data-last', jQuery(this).attr('data-file_url'))
                jQuery(jQuery('#youtube_url')[0]).attr('data-last', jQuery(this).attr('data-youtubeUrl'))
                jQuery(jQuery('#faq_theme')[0]).attr('data-last', jQuery(this).attr('data-faq_theme'))
                jQuery(jQuery('#apps_links')[0]).attr('data-checked', jQuery(this).attr('data-apps_links'))

                jQuery('#faqLastTheme')[0].value = jQuery(this).attr('data-faq_theme')

                jQuery('#removeTitle')[0].value = jQuery(this).attr('data-title')
            })
        });

        function openTab(evt, tabName) {
            let i, tabcontent, tablinks;

            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        document.getElementById("defaultOpen").click();

        function openDropdown() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        function filterFunction() {
            let input, filter, ul, li, option, i;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            div = document.getElementById("myDropdown");
            option = div.querySelectorAll(".option");
            for (i = 0; i < option.length; i++) {
                txtValue = option[i].textContent || option[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    option[i].style.display = "";
                } else {
                    option[i].style.display = "none";
                }
            }
        }
        </script>
        <div class="modal">
            <div class="modal-w">
                <h3>Are you sure you want to delete the article?</h3>
                <p>You will only remove the article from the article import file and will not be able to update it for the blog in the future.</p>
                <p>The page in the admin panel will switch to Draft status.</p>
                <p>For complete removal, go to the Wordpress admin panel, Pages section, find and delete permanently</p>
                <button id="cancelRemove">Cancel</button>
                <button id="DeleteArticle">Remove</button>
            </div>
        </div>
    </body>
</html>