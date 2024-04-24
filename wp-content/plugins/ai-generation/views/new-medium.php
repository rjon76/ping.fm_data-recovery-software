<?php

if( !function_exists("new_medium_data") ) {
    function new_medium_data(){ ?>
        <link rel="stylesheet" id="style-admin" href="<?php echo get_site_url();?>/wp-content/plugins/ai-generation/assets/css/style.css" media="all">
        <?php
            $uploads = wp_upload_dir()["basedir"];
            $file_proccess = $uploads . '/process_record.txt';
            $file = $uploads . '/time_record.txt';
            if(file_exists($file_proccess) && file_exists($file) && file_get_contents($file_proccess) !== 'done') {
                echo '<div class="nowproccess">'.
                        file_get_contents($file_proccess)
                        .' (proccess: '.file_get_contents($file)
                    .')</div>';
            }
        ?>
        <h1>Generate Medium article</h1>
        <div id="generateArticle" class="tabcontent">
            <form id="article">
                <label for="title">What would you like to write about (max 150 characters)</label>
                <input type="text" id="title" name="title" maxlength="150">
                <label for="h1title">H1 (Article Title) (max 150 characters)</label>
                <input type="text" id="h1title" name="h1title" maxlength="150">
                <label for="url">URL to pass link juice (Dofollow)</label>
                <input type="text" id="url" name="url">
                <label for="url_descr">What does this link lead to, and where will users be directed if they click on it? (max 150 characters)</label>
                <input type="text" id="url_descr" name="url_descr" maxlength="150">
                <label for="anchor">Link Anchor (ex: “how to do something” Don’t spam!)</label>
                <input type="text" id="anchor" name="anchor">
                <label for="file">Featured Image (JPG only)</label>
                <input type="file" name="file" id="file">
                <label for="faq_theme">Please provide me with a keyword or niche for which you want to generate a (FAQ) section and an AI image.</label>
                <input type="text" id="faq_theme" name="faq_theme">
                <label for="youtube_url">Add Youtube Link</label>
                <input type="text" id="youtube_url" name="youtube_url">
                <label for="article_tags">Tags comma separated</label>
                <input type="text" id="article_tags" name="article_tags">
                <label for="apps_links" class="checkbox">
                    <input type="checkbox" name="apps_links" id="apps_links">
                    Click here if you want your article to look like a “list of/best of” style (not a how to style)
                </label>
                <?php submit_button('AI Generate'); ?>
            </form>
        </div>
        <script>
            jQuery("#article").on("submit", function(event) {
                event.preventDefault();
                if( jQuery('#title')[0].value.trim().length === 0 ||
                    jQuery('#h1title')[0].value.trim().length === 0 ||
                    jQuery('#faq_theme')[0].value.trim().length === 0 ) {
                    alert('Required Fields: Title, H1, Faq');
                } else {
                    jQuery('#submit').attr('disabled','true');
                    const form  = new FormData(this);
                    let errorKey = false;
                    form.append('action', 'chat_gpt_generate_medium');
                    
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: form,
                        contentType: false,
                        processData: false
                    }).done(function(data){
                        console.log("data is:", data);
                        if(data === 'Empty OPENAI_API_KEY') {
                            alert(data);
                            errorKey = true;
                        }
                    }).fail(function(data){
                        alert("Something went wrong.");
                    })

                    setTimeout(() => {
                        if(!errorKey) {
                            alert("Article added to queue!");
                            // location.reload();
                        }
                    }, 3000);
                }
            });
        </script>
    <?php }
}
