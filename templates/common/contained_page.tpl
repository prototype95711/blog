<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width">

        {include file='components/head_styles.tpl'}

        <title>{$title}</title>
    </head>
    <body>
        {include file='components/header.tpl'}

        <main>
            <section class="main-content display-content-width">
                <div class="content {if $smarty.capture.sidebar}with-sidebar{/if}">
                    {$smarty.capture.content nofilter}
                </div>
            </section>
        </main>

        {include file='components/footer.tpl'}
    </body>
</html>