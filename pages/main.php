<style>
    .mainarea{
        background-color:white;
        border-radius: 10px;
        margin-top:10px;
        padding:8px;
        width:90%;
    }
    .important{
        color:red;
    }

    .duplicate-button{
        display:none;
        margin-top:15px;
        text-align:center;
    }
</style>
<div class="mainarea">
    <h1>Block Them All</h1>

    <form method="post" action="options.php">
    <input type="hidden" value="wpblockthemall_update" name="action" />
        <div class="form-control">
            <label>Block time</label>
            <input type="text" name="time" id="time" value="" />
        </div>
        <?php submit_button(); ?>
    </form>
</div>

<script type="text/javascript">

</script>