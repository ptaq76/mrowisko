{{-- MODAL EDYCJI ZLECENIA --}}
<div class="modal fade" id="editOrderModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" style="max-width:54%;min-width:600px">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px;overflow:hidden">

            <div class="modal-header text-white border-0 py-3 px-4" id="editOrderModalHeader"
                 style="background:linear-gradient(135deg,#6EBF58,#4da83e)">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title mb-0" id="editOrderModalTitle"
                        style="font-family:var(--font-display);font-size:18px;font-weight:800;letter-spacing:.05em">
                        EDYCJA ZLECENIA
                    </h5>
                    <span id="editOrderTypeBadge" class="badge"
                          style="font-size:10px;font-weight:700;letter-spacing:.08em;padding:3px 8px;border-radius:20px;background:rgba(0,0,0,.25);color:#fff;font-family:var(--font-display)">
                        PICKUP
                    </span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0" style="max-height:78vh;overflow-y:auto">
                <form id="editOrderForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="edit_order_id" name="order_id">
                    <input type="hidden" id="edit_order_type" name="type" value="pickup">

                    {{-- Data + Kierowca --}}
                    <div class="d-flex gap-3 px-4 pt-2 pb-2 border-bottom">
                        <div style="flex:0 0 180px">
                            <label class="om-label">DATA</label>
                            <input type="date" name="date" id="edit_order_date" class="form-control form-control-sm" required>
                        </div>
                        <div style="flex:1">
                            <label class="om-label">KIEROWCA</label>
                            <select name="driver_id" id="edit_order_driver" class="form-select form-select-sm" required onchange="onEditDriverChange()">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    {{-- Kontrahent --}}
                    <div class="om-row" id="edit_start_row">
                        <div class="om-key">KONTRAHENT</div>
                        <div class="om-val">
                            <select name="client_id" id="edit_order_client" class="form-select form-select-sm" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    {{-- LS --}}
                    <div id="edit_ls_section" style="display:none">
                        <div class="om-row om-row-wrap">
                            <div class="om-key">LIEFERSCHEIN</div>
                            <div class="om-val">
                                <input type="hidden" name="lieferschein_id" id="edit_order_ls_id">
                                <div class="input-group input-group-sm" style="flex-wrap:nowrap">
                                    <input type="text" id="edit_order_ls_display" class="form-control form-control-sm"
                                           readonly style="cursor:pointer;background:#fff" onclick="toggleEditLsTable()">
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="toggleEditLsTable()" title="Pokaż wolne LS"
                                            style="padding:0 10px;flex-shrink:0">
                                        <i class="fas fa-list-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="edit_ls_table_wrap" class="om-full-row" style="display:none;padding:0;border-top:1px solid var(--gray-2)">
                                <table class="table table-sm table-hover mb-0 w-100" style="font-size:11px">
                                    <thead style="position:sticky;top:0;background:#f4f5f7">
                                        <tr><th>Numer</th><th>Data</th><th>Towar</th><th>Okienko</th><th>Kierunek</th><th>Importer</th></tr>
                                    </thead>
                                    <tbody id="edit_ls_table_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Start --}}
                    <div class="om-row">
                        <div class="om-key">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <span>START</span>
                                <div class="d-flex gap-1">
                                    <button type="button" class="om-side-btn" id="edit_btn_leipa" onclick="setEditStart('leipa')" title="LEIPA">
                                        <i class="fas fa-industry"></i>
                                    </button>
                                    <button type="button" class="om-side-btn" id="edit_btn_ewrant" onclick="setEditStart('ewrant')" title="Ewrant" style="color:#e74c3c">
                                        <i class="fas fa-home"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="om-val">
                            <select name="start_client_id" id="edit_order_start" class="form-select form-select-sm" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    {{-- Ciągnik --}}
                    <div class="om-row">
                        <div class="om-key">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <span>CIĄGNIK</span>
                                <button type="button" class="om-side-btn" onclick="setEditFavTractor()" title="Ulubiony ciągnik" style="color:#f39c12">
                                    <i class="fas fa-star"></i>
                                </button>
                            </div>
                        </div>
                        <div class="om-val">
                            <select name="tractor_id" id="edit_order_tractor" class="form-select form-select-sm" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    {{-- Naczepa --}}
                    <div class="om-row">
                        <div class="om-key">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <span>NACZEPA</span>
                                <button type="button" class="om-side-btn" onclick="setEditFavTrailer()" title="Ulubiona naczepa" style="color:#f39c12">
                                    <i class="fas fa-star"></i>
                                </button>
                            </div>
                        </div>
                        <div class="om-val">
                            <select name="trailer_id" id="edit_order_trailer" class="form-select form-select-sm">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    {{-- Godzina --}}
                    <div class="om-row om-row-wrap">
                        <div class="om-key">GODZINA</div>
                        <div class="om-val">
                            <input type="text" name="planned_time" id="edit_order_time" class="form-control form-control-sm">
                        </div>
                        <div class="om-full-row">
                            @for($h = 4; $h <= 22; $h++)
                            <button type="button" class="om-qbtn"
                                    onclick="document.getElementById('edit_order_time').value='{{ $h }}:00'">{{ $h }}:00</button>
                            @endfor
                        </div>
                    </div>

                    {{-- Towary --}}
                    <div class="om-row om-row-wrap">
                        <div class="om-key">TOWARY</div>
                        <div class="om-val">
                            <input type="text" name="fractions_note" id="edit_order_goods" class="form-control form-control-sm" required>
                        </div>
                        <div class="om-full-row" id="edit_goods_buttons"></div>
                    </div>

                    {{-- Uwagi --}}
                    <div class="om-row om-row-wrap" style="border-bottom:none">
                        <div class="om-key">UWAGI</div>
                        <div class="om-val">
                            <textarea name="notes" id="edit_order_notes" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="om-full-row" id="edit_notes_buttons"></div>
                    </div>

                    {{-- Data na placu --}}
                    <div class="om-row" style="border-top:2px solid #f0f0f0;margin-top:4px">
                        <div class="om-key" style="color:#e67e22">
                            <i class="fas fa-industry me-1"></i> PLAC
                        </div>
                        <div class="om-val gap-2">
                            <input type="date" name="plac_date" id="edit_order_plac_date"
                                   class="form-control form-control-sm"
                                   title="Data widoczności zlecenia na placu">
                            <small class="text-muted text-nowrap">Widoczność na placu</small>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer border-0 px-4 py-3" style="background:#f8f9fa">
                <button type="button" class="btn btn-sm btn-danger me-auto" id="edit_order_delete_btn" onclick="deleteEditOrder()">
                    <i class="fas fa-trash"></i> Usuń
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Anuluj
                </button>
                <button type="button" class="btn btn-sm btn-add" onclick="submitEditOrder()">
                    <i class="fas fa-save"></i> <span id="edit_order_submit_label">Zapisz</span>
                </button>
            </div>
        </div>
    </div>
</div>
{{-- style dziedziczone z order_modal.blade.php --}}
