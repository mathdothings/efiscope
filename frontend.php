<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baixar NFe e NFCE | e-Fisco PE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        *,
        *::after,
        *::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        *::selection {
            color: white;
            background-color: #3DADE9;
        }

        body {
            font-size: 1rem;
            font-family: Arial, Helvetica, sans-serif;
            margin: 10px auto;
            background-color: oklch(93.2% 0.032 255.585);
            width: 100%;
            max-width: 100%;
        }

        h3 {
            margin: 0 auto;
            text-align: center;
            background-color: #f52b37;
            color: white;
            padding: 1rem;
            width: 50%;
            border-radius: 6px;
        }

        input[type="text"],
        textarea,
        td {
            font-family: 'Courier New', Courier, monospace;
        }

        fieldset {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        details {
            margin: 0;
        }

        .container {
            display: grid;
            background-color: oklch(98.5% 0.002 247.839);
        }

        .wrapper {
            display: flex;
            align-items: center;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .msg {
            background-color: #EFF6FF;
            border: 1px solid #BEDBFF;
            color: #51A2FF;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            width: 100%;
            text-align: center;
        }

        .copy-btn {
            padding: 5px 10px;
            background-color: oklch(92.8% 0.006 264.531);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            min-width: 60px;
            transition: background-color 0.3s;
        }

        .copy-btn:hover {
            background-color: #0056b3;
        }

        #tax-serie {
            width: 20%;
        }

        #submit-button {
            background-color: #2b7fff;
            color: white;
            margin: auto;
        }

        #submit-button:hover {
            background-color: royalblue;
        }

        #session-error {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #csm-logo {
            margin: 0 auto;
            margin-bottom: 2rem;
        }

        @media only screen and (max-width: 950px) {
            .container {
                width: 100%;
            }

            fieldset {
                display: grid;
                grid-template-columns: repeat(1, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php if (isset($message)) {
            echo "<p class='msg'>{$message}</p>";
        }
        ?>
        <form method="post" class="container" style="border: 1px solid #ccc; border-radius: 6px; padding: 1rem;">
            <img id="csm-logo" src="https://csmti.com.br/assets/images/logo.png" alt="CSM Logo">
            <fieldset>
                <legend>Tipo de nota</legend>
                <div>
                    <input type="radio" id="nfe" name="tax-type" required value="nfe" <?= $taxType === 'nfe' ? 'checked' : '' ?> />
                    <label for="nfe">NFE (Nota Grande)</label>
                </div>
                <div>
                    <input type="radio" id="nfce" name="tax-type" required value="nfce" <?= $taxType === 'nfce' ? 'checked' : '' ?> />
                    <label for="nfce">NFCE (Cupom)</label>
                </div>
            </fieldset>
            <br />

            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <label for="session" style="text-align:center; min-width: 120px;">ID Sessão</label>
                <input name="session" id="session" type="text" required value='<?= $session ?? '' ?>' style="flex: 1;">
                <!-- <button type="button" class="copy-btn" data-target="session">Copiar</button> -->
            </div>

            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <label for="js-session" style="text-align:center; min-width: 120px;">JS Sessão</label>
                <input name="js-session" id="js-session" type="text" required value='<?= $jsSession ?? '' ?>' style="flex: 1;">
                <!-- <button type="button" class="copy-btn" data-target="js-session">Copiar</button> -->
            </div>

            <fieldset>
                <legend>Dados do Usuário</legend>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="cd-user" style="text-align:center; min-width: 120px;">Código do Usuário</label>
                    <input name="cd-user" id="cd-user" type="text" required value='<?= $user ?? '' ?>' style="flex: 1;">
                    <!-- <button type="button" class="copy-btn" data-target="cd-user">Copiar</button> -->
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="ie-emit" style="text-align:center; min-width: 120px;">I.E. Emitente</label>
                    <input name="ie-emit" id="ie-emit" type="text" required value='<?= $ieEmit ?? '' ?>' style="flex: 1;">
                    <button type="button" class="copy-btn" data-target="ie-emit">Copiar</button>
                </div>
            </fieldset>
            <br />
            <fieldset>
                <legend>Tipo de contribuinte</legend>
                <div>
                    <input type="radio" id="emitente" name="contribuition-type" value="E" <?= $contribuitionType === 'E' ? 'checked' : '' ?> />
                    <label for="emitente">Emitente (Saída)</label>
                </div>
                <div>
                    <input type="radio" id="destinatario" value="D" name="contribuition-type" <?= $contribuitionType === 'D' ? 'checked' : '' ?> />
                    <label for="destinatario">Destinatário (Entrada)</label>
                </div>
            </fieldset>
            <br>
            <div class="small-input">
                <fieldset>
                    <legend>Datas</legend>
                    <div>
                        <label for="date-start">Data Inicial</label>
                        <input name="date-start" id="date-start" type="date" value='<?= $dateStart ?? '' ?>'>
                    </div>
                    <div>
                        <label for="date-end">Data Final</label>
                        <input name="date-end" id="date-end" type="date" value='<?= $dateEnd ?? '' ?>'>
                    </div>
                </fieldset>
                <br />
                <label for="keys-list">Chaves de Acesso</label>
                <textarea name="keys-list" id="keys-list" pattern="(\d{44}\s*)+"><?=
                                                                                    htmlspecialchars($_POST['keys-list'] ?? '', ENT_QUOTES)
                                                                                    ?></textarea>
                <br />
                <label for="tax-number">Número da Nota</label>
                <textarea name="tax-number" id="tax-number" pattern="(\d{9}\s*)+"><?=
                                                                                    htmlspecialchars($_POST['tax-number'] ?? '', ENT_QUOTES)
                                                                                    ?></textarea>
                <label for="tax-serie">Série</label>
                <input id="tax-serie" name="tax-serie" type="number" value='<?= $taxSerie ?? '' ?>'>
            </div>
            <br>
            <input type="submit" id="submit-button" name="start" value="Iniciar" class="small-input"></input>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.copy-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);

                    if (input) {
                        input.select();
                        input.setSelectionRange(0, 99999);

                        navigator.clipboard.writeText(input.value).then(() => {
                            const originalText = this.textContent;
                            this.textContent = '✓';
                            this.style.backgroundColor = 'oklch(79.2% 0.209 151.711)';

                            setTimeout(() => {
                                this.textContent = originalText;
                                this.style.backgroundColor = '';
                            }, 2000);
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>