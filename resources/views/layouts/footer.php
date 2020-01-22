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
<script src="node_modules/todomvc-common/base.js"></script>
<script src="/js/app.js"></script>
</body>
</html>

<script>
    updateCounter();

    function updateCounter() {
        var count = $("ul#todo-list > li.completed").length;
        document.getElementById("completed-task-counter").innerHTML = count + ' item left';
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

            $.ajax({
                url: url,
                method: 'GET',
                data: data,
                dataType: 'html',
                success: function (result) {
                    var ul = document.getElementById("todo-list");
                    ul.innerHTML = result + ul.innerHTML;

                    document.getElementById("new-todo").value = '';
                },
                error: function (result) {
                }
            });
        }
    }

    function deleteTask(task_id) {

        var url = '/delete-task';
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
                    } else {
                        document.getElementById('task-' + data.task_id).classList.remove('completed');
                    }

                    updateCounter()
                }
            },
            error: function (result) {
            }
        });
    }
</script>