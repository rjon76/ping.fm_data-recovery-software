<?php

add_action('page_row_actions', 'regenerate_article', 10, 2);

function regenerate_article($actions, $page_object)
{

    $uploads = wp_upload_dir()["basedir"];
    $file_queue = $uploads . '/regeneration_queue_ids.txt';
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

    $actions['regenerate_link'] = $isAddedInQueue ? 
        '<span>' . __('Regeneration in Queue') . '</span>' :
        '<a href="edit.php?action=chat_gpt_generate_article&postId='. $page_object->ID .'" class="regenerate">' . __('Regenerate Article') . '</a>' .
        '<script>
            jQuery(".regenerate").each(function(key, el) {
                jQuery(el).on("click", function(event) {
                    event.stopImmediatePropagation();
                    event.preventDefault();
                    if (window.confirm("Do you really want regenerate article?")) {
                        if(jQuery(this).hasClass("disabled")) { return; }
                        
                        jQuery.ajax({
                            url: jQuery(this).attr("href"),
                            type: "GET",
                        }).done(function(data){
                            console.log("data is:", data);
                        }).fail(function(data){
                            alert("Something went wrong.");
                            location.reload();
                        })

                        jQuery(this).addClass("disabled");
                        jQuery(this).attr("href", "");

                        setTimeout(() => {
                            alert("Article regeneration added to queue!");
                            window.location.href = "admin.php?page=new-post-generation";
                        }, 1000);
                    }
                })
            })
        </script>';

    return $actions;
}