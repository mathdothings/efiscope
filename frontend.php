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
        }

        h3 {
            margin: 0 auto;
            text-align: center;
            background-color: #f52b37;
            color: white;
            padding: 1rem;
            width: 50%;
            border-radius: 10px;
        }

        input[type="text"],
        textarea,
        td {
            font-family: 'Courier New', Courier, monospace;
        }

        .container {
            display: grid;
            width: 70%;
        }

        .msg {
            background-color: #EFF6FF;
            border: 1px solid #BEDBFF;
            color: #51A2FF;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            width: 100%;
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @media only screen and (max-width: 500px) {
            .container {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div style="display: flex; align-items: center; flex-direction: column;">
        <?php if ($message) {
        ?>
            <p class="msg"><?= $message ?></p>
        <?php } ?>
        <form method="post" class="container" style="border: 1px solid #ccc; border-radius: 10px; padding: 1rem;">
            <fieldset style="display: grid; grid-template-columns: repeat(2, 1fr);">
                <legend>Tipo de Nota:</legend>
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
            <label for="session">ID Sessão</label>
            <input name="session" id="session" type="text" required value='<?= $session ?? '' ?>'>
            <br />
            <label for="js-session">JS Sessão</label>
            <input name="js-session" id="js-session" type="text" required value='<?= $jsSession ?? '' ?>'>
            <br />
            <div style="display: grid; grid-template-columns: repeat(2, 1fr);">
                <div>
                    <label for="cd-user">Código do Usuário</label>
                    <input name="cd-user" id="cd-user" type="text" required value='<?= $user ?? '' ?>'>
                </div>
                <div>
                    <label for="ie-emit">I.E. Emitente</label>
                    <input name="ie-emit" id="ie-emit" type="text" required value='<?= $ieEmit ?? '' ?>'>
                </div>
            </div>
            <br />
            <fieldset style="display: grid; grid-template-columns: repeat(2, 1fr);">
                <legend>Tipo de contribuinte:</legend>
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
                <div style="display: grid; grid-template-columns: repeat(2, 1fr);">
                    <div>
                        <label for="date-start">Data Inicial</label>
                        <input name="date-start" id="date-start" type="date" value='<?= $dateStart ?? '' ?>'>
                    </div>
                    <div>
                        <label for="date-end">Data Final</label>
                        <input name="date-end" id="date-end" type="date" value='<?= $dateEnd ?? '' ?>'>
                    </div>
                </div>
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
</body>

</html>