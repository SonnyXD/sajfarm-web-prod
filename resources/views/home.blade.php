<x-layout>
<div class="row">
<div class="col-sm-3 grid-margin">
    <div class="card">
        <div class="card-body">
        <div>
            <p class="card-title card-title-dash font-weight-medium">NIRuri</p>
            <h3 class="rate-percentage d-flex justify-content-between">{{$nirs}}
        </div>
        </div>
    </div>
    </div>
    <div class="col-sm-3 grid-margin">
    <div class="card">
        <div class="card-body">
        <div>
            <p class="card-title card-title-dash font-weight-medium">Transferuri</p>
            <h3 class="rate-percentage d-flex justify-content-between">{{$transfers}}
        </div>
        </div>
    </div>
    </div>
    <div class="col-sm-3 grid-margin">
    <div class="card">
        <div class="card-body">
        <div>
            <p class="card-title card-title-dash font-weight-medium">Consumuri</p>
            <h3 class="rate-percentage d-flex justify-content-between">{{$consumptions}}
        </div>
        </div>
    </div>
    </div>
    <div class="col-sm-3 grid-margin">
    <div class="card">
        <div class="card-body">
        <div>
            <p class="card-title card-title-dash font-weight-medium">Retururi</p>
            <h3 class="rate-percentage d-flex justify-content-between">{{$returnings}}
        </div>
        </div>
    </div>
    </div>
    <div class="col-lg-12">
							<div class="card px-3">
								<div class="card-body">
									<h4 class="card-title">To Do List</h4>
									<div class="list-wrapper">
										<ul class="d-flex flex-column-reverse todo-list">
											@if($tasks->count())
												@foreach($tasks as $task)
												<li>
												<div class="form-check">
													<label class="form-check-label">
														<input class="checkbox" type="checkbox">
														{{$task->task}}
													<i class="input-helper"></i></label>
												</div>
												<!-- <i class="remove ti-close"></i> -->
												<form method="POST" id="delete-task-{{$task->id}}" action="{{route('task.destroy', $task->id)}}" style="margin-left: auto;">
												@csrf
												<button style="margin-left: auto; padding: 0.275rem 0.444rem" type="submit" class="btn btn-danger">X</button>
												</form>
											</li>
												@endforeach
											@endif
										</ul>
									</div>
                  <div class="add-items d-flex">
				  						<x-form method="POST" id="tasks" action="{{route('task.store')}}">
										<x-input type="text" class="form-control todo-list-input" id="add-task" name="add-task" placeholder="Adauga task nou"/>
										<x-button><i class="ti-location-arrow"></i></x-button>
										</x-form>
									</div>
								</div>
							</div>
						</div>
    </div>
</x-layout>

@if ($errors->any())
@foreach($errors->all() as $error)
<script>
swal("Eroare", 'A intervenit o eroare! Incearca din nou!', "error");
</script>
<p>{{$error}}</p>
@endforeach
@endif

@if(Session::has('success'))
<script>
swal("Succes", "Te-ai conectat cu succes!", "success");
</script>
@endif

@if(Session::has('done'))
<script>
swal("Succes", "Ai adaugat task-ul cu succes!", "success");
</script>
@endif

@if(Session::has('deleted'))
<script>
swal("Succes", "Ai sters task-ul cu succes!", "success");
</script>
@endif