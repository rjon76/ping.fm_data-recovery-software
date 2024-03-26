<?php

add_action('page_row_actions', 'add_faq_questions', 10, 2);

function add_faq_questions($actions, $page_object)
{

    $uploads = wp_upload_dir()["basedir"];
    $file_queue = $uploads . '/add_questions_faq_queue_ids.txt';
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

    $actions['add_questions_link'] = $isAddedInQueue ? 
        '<span>' . __('Added 10 FAQ in Queue') . '</span>' :
        '<a href="edit.php?action=chat_gpt_generate_article&postId='. $page_object->ID .'&addQuestions=true" class="addten">' . __('Add 10 FAQ') . '</a>' .
        '<script>
            jQuery(".addten").each(function(key, el) {
                jQuery(el).on("click", function(event) {
                    event.stopImmediatePropagation();
                    event.preventDefault();
                    if (window.confirm("Do you really want add 10 FAQ questions?")) {
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
                            alert("Add 10 questions to FAQ generation!");
                            window.location.href = "admin.php?page=new-post-generation";
                        }, 1000);
                    }
                })
            })
        </script>';

    return $actions;
}