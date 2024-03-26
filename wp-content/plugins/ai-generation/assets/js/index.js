window.addEventListener('DOMContentLoaded', () => {
    
    const $ = jQuery;

    function getQueryVariable(variable, query) {
        const vars = query.split('&');
        for (let i = 0; i < vars.length; i++) {
            const pair = vars[i].split('=');
            if (decodeURIComponent(pair[0]) == variable) {
                return decodeURIComponent(pair[1]);
            }
        }
        console.log('Query variable %s not found', variable);
    }

    $('.js-wpml-translate-link').on('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        $('#dialogEditTranslate').show();
        $('#translatePage').on('click', function() {
            const langCode = getQueryVariable('lang', $(e.currentTarget).attr('href').replace('post.php?', ''));
            const postID = getQueryVariable('post', $(e.currentTarget).attr('href').replace('post.php?', ''));
            let urlTranslate = 'edit.php?action=chat_gpt_translate_article&postId=' + postID + '&fullLanguage=' + langCode; 
            if(!postID) {
                const trID = getQueryVariable('trid', $(e.currentTarget).attr('href').replace('post-new.php?', ''));
                const trLangCode = getQueryVariable('lang', $(e.currentTarget).attr('href').replace('post-new.php?', ''));
                urlTranslate = 'edit.php?action=chat_gpt_translate_article&trid=' + trID + '&fullLanguage=' + trLangCode;
            }
            
            jQuery.ajax({
                url: urlTranslate,
                type: "GET",
            }).done(function(data){
                console.log("data is:", data);
            }).fail(function(data){
                alert("Something went wrong.");
                location.reload();
            })

            $(e.currentTarget).addClass("disabled");
            $(e.currentTarget).attr("href", "");

            setTimeout(() => {
                alert("Translate added to queue!");
                window.location.href = "admin.php?page=new-post-generation";
            }, 1000);
        })
        
        $('#editPage').on('click', function() {
            window.location.href = $(e.currentTarget).attr('href');
        })
    });
});