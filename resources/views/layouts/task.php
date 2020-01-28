<li id="task-<?php echo $task->getId() ?>" class=<?php echo ($task->isActive() == false) ? 'completed' : '' ?> >
    <div class="view">

        <input id="toggle-<?php echo $task->getId() ?>" 
               onchange="toggleTask(this, <?php echo $task->getId() ?>)" 
               class="toggle" 
               type="checkbox" <?php echo ($task->isActive() == false) ? "checked" : "" ?> >

        <label id="label-<?php echo $task->getId() ?>" ondblclick="setFocusForTask(this, <?php echo $task->getId() ?>)">
            <?php echo $task->getName() ?>
        </label>
        
        <button class="destroy" onclick="deleteTask(<?php echo $task->getId() ?>)">
        </button>
        
    </div>

    <input id="edit-<?php echo $task->getId() ?>" 
           class="edit" value="<?php echo $task->getName() ?>" 
           onblur="updateTask(this, <?php echo $task->getId() ?>)" 
           onkeypress="editTask(this, <?php echo $task->getId() ?>)">
</li>
