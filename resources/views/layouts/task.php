<li id="task-<?php echo $task['id'] ?>" class=<?php echo ($task['status'] == 0) ? 'completed' : '' ?> >
    <div class="view">

        <input id="toggle-<?php echo $task['id'] ?>" 
               onchange="toggleTask(this, <?php echo $task['id'] ?>)" 
               class="toggle" 
               type="checkbox" <?php echo ($task['status'] == 0) ? "checked" : "" ?> >

        <label id="label-<?php echo $task['id'] ?>" ondblclick="setFocusForTask(this, <?php echo $task['id'] ?>)">
            <?php echo $task["name"] ?>
        </label>
        
        <button class="destroy" onclick="deleteTask(<?php echo $task['id'] ?>)">
        </button>
        
    </div>
    
    <input id="edit-<?php echo $task["id"] ?>" 
           class="edit" value="<?php echo $task["name"] ?>" 
           onblur="updateTask(this, <?php echo $task["id"] ?>)" 
           onkeypress="editTask(this, <?php echo $task["id"] ?>)">
</li>
