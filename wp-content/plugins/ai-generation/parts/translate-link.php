<?php

add_action('page_row_actions', 'translate_article', 10, 2);

function translate_article($actions, $page_object)
{

    $uploads = wp_upload_dir()["basedir"];
    $file_queue = $uploads . '/translation_queue_ids.txt';
    $isAddedInQueue = false;
    if(file_exists($file_queue)) {
        $stringsID = file_get_contents($file_queue);
        $aIDs = explode(',', $stringsID);

        foreach($aIDs as $ids) {

            if(!empty($ids) && strlen($ids) > 0) {

                if($page_object->ID === intval($ids)) {
                    $isAddedInQueue = true;
                }
            }
        }
    }

    $actions['translate_link'] = $isAddedInQueue ? 
        '<span>' . __('Translate in Queue') . '</span>' :
        '<a href="edit.php?action=chat_gpt_translate_article&postId='. $page_object->ID .'" class="translate">' . __('Translate Article') . '</a>' .
        '<script>
            jQuery(".translate").each(function(key, el) {
                jQuery(el).on("click", function(event) {
                    event.stopImmediatePropagation();
                    event.preventDefault();
                    if (window.confirm("Do you really want translate article?")) {
                        if(jQuery(this).hasClass("disabled")) { return; }
                        let errorKey = false;
                        jQuery.ajax({
                            url: jQuery(this).attr("href"),
                            type: "GET",
                        }).done(function(data){
                            console.log("data is:", data);
                            if(data === "Empty OPENAI_API_KEY") {
                                alert(data);
                                errorKey = true;
                            }
                        }).fail(function(data){
                            alert("Something went wrong.");
                            location.reload();
                        })

                        jQuery(this).addClass("disabled");
                        jQuery(this).attr("href", "");

                        setTimeout(() => {
                            if(!errorKey) {
                                alert("Translate added to queue!");
                                window.location.href = "admin.php?page=new-post-generation";
                            }
                        }, 1000);
                    }
                })
            })
        </script>';
    return $actions;
}