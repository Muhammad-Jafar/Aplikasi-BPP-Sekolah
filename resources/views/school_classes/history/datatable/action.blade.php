<div class="btn-group" role="group">
    <div class="mx-1">
        <form action="{{ route('school-classes.restore.history', $model->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success restore-button">
                <i class="bi bi-arrow-bar-left"></i>
            </button>
        </form>
    </div>

    <div class="mx-1">
        <form action="{{ route('school-classes.destroy.history', $model->id) }}" method="POST">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger delete-permanent-button">
                <i class="bi bi-trash-fill"></i>
            </button>
        </form>
    </div>
</div>
