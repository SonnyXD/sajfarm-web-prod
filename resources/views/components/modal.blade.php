<div class="modal fade show" id="meds-modal" tabindex="-1" aria-labelledby="ModalLabel" style="display: none;" aria-modal="true" role="dialog">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="ModalLabel">Adauga Pozitie</h5>
                          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          {{$slot}}
                        </div>
                        <div class="modal-footer">
                          <x-button type="button" class="btn btn-success" id="add-product">Adauga</x-button>
                          <x-modal-trigger type="button" class="btn btn-light" data-bs-dismiss="modal">Inchide</x-modal-trigger>
                        </div>
                      </div>
                    </div>
                  </div>