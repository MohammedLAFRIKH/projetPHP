<?php

// FooterView.php
namespace App\Views\EspaceProf;

class FooterProfView {
    public function showFooter() {
        ?>
            </main>
            </div>
        </div>

        <!-- Ajoutez les liens vers Bootstrap CSS et JS avant la fermeture de la balise </body> -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

        <script type="text/javascript" src="<?= BASE_URL ?>/public/js/arabic_keyboard-7aa1cfa21c01341f1902743f091bde0a.js" charset="UTF-8"></script>
        </body></html>';
        <?php
    }
}
