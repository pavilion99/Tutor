<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button"
                    class="navbar-toggle collapsed"
                    data-toggle="collapse"
                    data-target="#topnav"
                    aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand"
               href="<?php echo REL; ?>">BITCHES</a>
        </div>
        <div class="collapse navbar-collapse"
             id="topnav">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="javascript:void(0);"
                       class="dropdown-toggle"
                       data-toggle="dropdown"
                       role="button"
                       aria-haspopup="true"
                       aria-expanded="false">Account <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo REL; ?>account">View Profile</a></li>
                        <li><a href="<?php echo REL; ?>account/edit">Edit Profile</a></li>
                    </ul>
                </li>
            </ul>
            <?php if (PAGE_NAME != "LOGIN"): ?>
                <ul class="nav navbar-nav navbar-right">
                    <?php if (!isset($_SESSION["id"]) || $_SESSION["id"] == -1): ?>
                        <li><a href="<?php echo REL; ?>login"><span class="glyphicon glyphicon-log-in"></span></a></li>
                    <?php else: ?>
                        <li><a onclick="logout()"><span class="glyphicon glyphicon-log-out"></span></a></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>