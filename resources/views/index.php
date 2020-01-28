<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Template • TodoMVC</title>
        <!-- CSS overrides - remove if you don't need it -->
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body>
        <?php if ($_SESSION['email']) { ?>
            <br>
            <a href="/auth/logout">logout</a>
            <br>
        <?php } ?>
            
        <?php if(isset($tasklist_hash)) {?>
            <br>
            <a href="/">Home</a>
        <?php } ?>

        <section class="todoapp">
            <header class="header">
                <h1>todos</h1>
                <input id="new-todo" class="new-todo" placeholder="What needs to be done?" autofocus onkeypress="return addTask(event)">
            </header>
            <!-- This section should be hidden by default and shown when there are todos -->
            <section class="main">
                <input id="toggle-all" class="toggle-all" type="checkbox" onclick="toggleAll(this)">
                <label for="toggle-all">Mark all as complete</label>

                <ul id="todo-list" class="todo-list">
                    <?php foreach ($list as $key => $task) { ?>
                        <?php include("layouts/task.php"); ?>
                    <?php } ?>
                </ul>

            </section>
            <!-- This footer should hidden by default and shown when there are todos -->
            <footer class="footer">
                <!-- This should be `0 items left` by default -->
                <span id="completed-task-counter" class="todo-count">0 item left</span>
                <!-- Remove this if you don't implement routing -->
                <ul class="filters">
                    <li>
                        <a onclick="showAll(this)" class="selected" href="#/">All</a>
                    </li>
                    <li>
                        <a onclick="showActive(this)" href="#/active">Active</a>
                    </li>
                    <li>
                        <a onclick="showCompleted(this)" href="#/completed">Completed</a>
                    </li>
                </ul>
                <!-- Hidden if no completed items are left ↓ -->
                <button id="clear-completed" onclick="clearCompeted()" class="clear-completed">Clear completed</button>
            </footer>
        </section>
            
        <?php if (count($shared_tasks_for_user)) { ?>
            <h1>Shared task for user</h1>

            <style type="text/css">
                .tg  {border-collapse:collapse;border-spacing:0;}
                .tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
                .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
                .tg .tg-9wq8{border-color:inherit;text-align:center;vertical-align:middle}
                .tg .tg-c3ow{border-color:inherit;text-align:center;vertical-align:top}
            </style>

            <table  id="shared-users" class="tg">
                <tr>
                    <th class="tg-9wq8">email</th>
                </tr>

                <?php foreach ($shared_tasks_for_user as $shared_task_for_user) { ?>
                    <tr>
                        <td class="tg-c3ow">
                            <a href="/shared?tasklist_hash=<?php echo $shared_task_for_user->getHash(); ?>"><?php echo $shared_task_for_user->getOwnerUser()->getEmail(); ?></a>
                        </td>
                    </tr>
                <?php } ?>

            </table>
        <?php } ?>

        <?php if( ! isset($tasklist_hash)) {?>
        <h1>Add user</h1>
        <form method="post" action='/add-shared-user'>
            <input type="text" name="email">
            <input name="rules" class="toggle" type="checkbox" class="checked" value="w">
            <label>(R/W)</label>

            <input type="submit" value="Add">
        </form>

            <?php if (count($shared_users)) { ?>
            <h1>Shared users</h1>

            <table  id="shared-users" class="tg">
                <tr>
                    <th class="tg-9wq8">email</th>
                    <th class="tg-c3ow">mode</th>
                    <th class="tg-c3ow"></th>
                </tr>

                <?php foreach ($shared_users as $shared_user) { ?>
                <tr id="shared-user-<?php echo $shared_user->getId(); ?>">
                    <td class="tg-c3ow">
                        <?php echo $shared_user->getSharedUser()->getEmail(); ?>
                    </td>
                    <td class="tg-c3ow">
                        <input id="toggle-mode-<?php echo $shared_user->getId(); ?>"
                               type="checkbox" 
                               onclick="toggleMode(this, <?php echo $shared_user->getId(); ?>)"
                               <?php echo ($shared_user->getMode() == 'w') ? "checked" : "" ?>
                               value="<?php echo $shared_user->getMode(); ?>">
                    </td>
                    <td class="tg-c3ow">
                        <button onclick="deleteSharedUser(<?php echo $shared_user->getId(); ?>)">Delete</button>
                    </td>
                </tr>
                <?php } ?>

            </table>
            <?php } ?>
        <?php } ?>

        <footer class="info">
            <p>Double-click to edit a todo</p>
            <!-- Remove the below line ↓ -->
            <p>Template by <a href="http://sindresorhus.com">Sindre Sorhus</a></p>
            <!-- Change this out with your name and url ↓ -->
            <p>Created by <a href="http://todomvc.com">you</a></p>
            <p>Part of <a href="http://todomvc.com">TodoMVC</a></p>
        </footer>
        <!-- Scripts here. Don't remove ↓ -->
        <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script src="/js/app.js"></script>
    </body>
</html>

<script>   
    updateCounter();

    toggleClearCompetedButton();

    toggleToggleAllButton();
    
    function deleteSharedUser(shared_task_id) {
        var url = '/delete-shared-user';
        var data = {'shared_task_id' : shared_task_id};

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'html',
            success: function (data) {
                data = JSON.parse(data);
                if (data.result == 'success')
                {
                    $('#shared-user-' + data.id).remove();
                }
            },
            error: function (result) {
            }
        });
    }

    function toggleMode(element, shared_task_id) {
        var url = '/toggle-mode';
        var data = {'shared_task_id' : shared_task_id};

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'html',
            success: function (data) {
                data = JSON.parse(data);
                if (data.result == 'success')
                {
                    
                }
            },
            error: function (result) {
            }
        });
    }

    function toggleToggleAllButton() {
        if ($("ul#todo-list > li.completed").length == $("ul#todo-list > li").length) {
            document.getElementById('toggle-all').checked = true;
        }
        else {
            document.getElementById('toggle-all').checked = false;
        }
    }

    function toggleClearCompetedButton() {
        if ($("ul#todo-list > li.completed").length == 0) {
            document.getElementById('clear-completed').classList.add('hidden');
        }
        else {
            document.getElementById('clear-completed').classList.remove('hidden');
        }
    }
    
    function clearCompeted() {
        ids = [];
        var ul = $("ul#todo-list > li.completed").each(function () {
            var params = $(this)[0].id.split('-');
            ids.push(params[params.length - 1]);
            
        });
        
        var url = '/close-completed-tasks';
        var data = {'ids': ids};

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'html',
            success: function (data) {
                data = JSON.parse(data);
                if (data.result == 'success')
                {
                    ul = document.getElementById("todo-list");
                    for(let i = 0; i < data.ids.length; i++) {
                        var candidate = document.getElementById("task-" + data.ids[i]);
                        ul.removeChild(candidate);
                    }

                    toggleClearCompetedButton();

                    updateCounter();
                }
            },
            error: function (result) {
            }
        });
    }

    function showActive(element) {
        $("ul#todo-list > li").each(function () {
            if ($(this).hasClass('completed')) {
                $(this).addClass('hidden');
            }
            else {
                $(this).removeClass('hidden');
            }
        });

        $("ul.filters > li > a").each(function () {
            $(this).removeClass('selected')
        });
        
        element.classList.add('selected');
    }

    function showCompleted(element) {
        $("ul#todo-list > li").each(function () {
            if (!$(this).hasClass('completed')) {
                $(this).addClass('hidden');
            }
            else {
                $(this).removeClass('hidden');
            }
        });
        
        $("ul.filters > li > a").each(function () {
            $(this).removeClass('selected')
        });
        
        element.classList.add('selected');
    }
    
    function showAll(element) {
        $("ul#todo-list > li").each(function () {
            $(this).removeClass('hidden');
        });
        
        $("ul.filters > li > a").each(function () {
            $(this).removeClass('selected')
        });
        
        element.classList.add('selected');
    }
    
    function updateCounter() {
        var completed_count = $("ul#todo-list > li.completed").length;
        var count = $("ul#todo-list > li").length;
        
        var left_count = count - completed_count;

        document.getElementById("completed-task-counter").innerHTML = left_count.toString() + ' item left';
    }

    function updateTask(element, task_id) {
        if (element.value == document.getElementById('label-' + task_id).innerText.trim()) {
            document.getElementById('task-' + task_id).classList.remove('editing');
            return;
        }

        var url = '/update-task';
        var data = {'id': task_id, 'name': element.value};

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'html',
            success: function (data) {
                data = JSON.parse(data);
                if (data.result == 'success') {
                    document.getElementById('task-' + data.task_id).classList.remove('editing');
                    document.getElementById('edit-' + data.task_id).value = data.name;
                    document.getElementById('label-' + data.task_id).innerText = data.name;
                }
            },
            error: function (result) {
            }
        });
    }

    function editTask(element, task_id) {
        if (event.keyCode == 13) {
            updateTask(element, task_id);
        }
    }

    function setFocusForTask(element, task_id) {
        document.getElementById('task-' + task_id).classList.add('editing');
        document.getElementById('edit-' + task_id).focus();
    }

    function addTask(event) {

        if (event.keyCode == 13) {
            var url = '/add-task';
            var data = {'name': event.target.value};
            
            <?php if (isset($tasklist_hash)) { ?>
                data.hash = "<?php echo $tasklist_hash ?>";
            <?php } ?>
                
                console.log(data);

            $.ajax({
                url: url,
                method: 'GET',
                data: data,
                dataType: 'html',
                success: function (result) {
                    var ul = document.getElementById("todo-list");
                    ul.innerHTML = result + ul.innerHTML;

                    document.getElementById("new-todo").value = '';
                    
                    updateCounter();
                },
                error: function (result) {
                }
            });
        }
    }

    function deleteTask(task_id) {

        var url = '/delete-task';
        var data = {'id': task_id};
        
        <?php if (isset($tasklist_hash)) { ?>
            data.hash = "<?php echo $tasklist_hash ?>";
        <?php } ?>

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'html',
            success: function (data) {
                data = JSON.parse(data);
                if (data.result == 'success')
                {
                    var ul = document.getElementById("todo-list");
                    var candidate = document.getElementById("task-" + data.task_id);
                    ul.removeChild(candidate);

                    updateCounter();
                }
            },
            error: function (result) {
            }
        });
    }

    function toggleAll(element) {
        var url = '/open-all-tasks';

        if (element.checked)
        {
            url = '/close-all-tasks';
        }

        var data = {};
        <?php if(isset($tasklist_hash)) {?>
            data = {'hash' : "<?php echo $tasklist_hash ?>"};
        <?php } ?>

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'html',
            success: function (data) {
                data = JSON.parse(data);
                if (data.result == 'success')
                {
                    if (document.getElementById('toggle-all').checked) {
                        $("ul#todo-list > li").each(function () {
                            $(this).addClass('completed');
                        });

                        $('input[type=checkbox].toggle').each(function () {
                            $(this).prop('checked', true);
                        });
                    } else {
                        $("ul#todo-list > li").each(function () {
                            $(this).removeClass('completed');
                        });

                        $('input[type=checkbox].toggle').each(function () {
                            $(this).prop('checked', false);
                        });
                    }

                    toggleClearCompetedButton();

                    updateCounter()
                }
            },
            error: function (result) {
            }
        });
    }

    function toggleTask(element, task_id) {

        var url = '/open-task';

        if (element.checked)
        {
            url = '/close-task';
        }

        var data = {'id': task_id};

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'html',
            success: function (data) {
                data = JSON.parse(data);
                if (data.result == 'success')
                {
                    if (document.getElementById('toggle-' + data.task_id).checked) {
                        document.getElementById('task-' + data.task_id).classList.add('completed');
                    } 
                    else {
                        document.getElementById('task-' + data.task_id).classList.remove('completed');
                    }
                    
                    toggleToggleAllButton();

                    toggleClearCompetedButton();

                    updateCounter()
                }
            },
            error: function (result) {
            }
        });
    }
</script>