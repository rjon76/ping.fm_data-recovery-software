<?php

function funcGenerateModal() {
    echo '<style>
        #dialogEditTranslate {
            position: fixed;
            background: white;
            z-index: 9999999;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 50px;
            box-shadow: 0px 0px 2px #000;
        }
        #dialogEditTranslate p {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .modalbtns {
            display: flex;
            gap: 20px;
            margin-top: 24px;
        }
        #dialogEditTranslate button {
            flex: 1 0;
            font-size: 18px;
        }
    </style>
    <div id="dialogEditTranslate" style="display:none;">
        <p>Re-translate or edit post?</p>
        <div class="modalbtns">
            <button id="editPage" class="button button-primary">Edit</button>
            <button id="translatePage" class="button button-primary button-green">Re-Translate</button>
        </div>
    </div>';
}