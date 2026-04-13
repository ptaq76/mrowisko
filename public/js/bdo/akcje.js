// Funkcje globalne dla akcji BDO
window.potwierdzMase = function(kartaId, wasteMass, nazwaPrzekazujacego, wasteCode, kpoId) {
    const formattedMass = parseFloat(wasteMass).toFixed(3).replace('.', ',');
    
    Swal.fire({
        title: 'Czy potwierdzić masę?',
        html: `
            <div class="mt-2">
                <p class="mb-2" style="font-size: 0.9rem;">${nazwaPrzekazujacego}</p>
                <p class="mb-3 fw-bold" style="font-size: 1.1rem;">${wasteCode}</p>
                <h2 class="display-4 fw-bold">${formattedMass}</h2>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Tak',
        cancelButtonText: 'Nie'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Przetwarzanie...',
                text: 'Wysyłanie do BDO',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/biuro/bdo/potwierdz-karte',
                method: 'POST',
                data: {
                    karta_id: kartaId,
                    waste_mass: parseFloat(wasteMass),
                    kpo_id: kpoId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        Swal.fire({
                            title: 'Potwierdzono',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Błąd',
                            text: data.message || 'Wystąpił błąd podczas potwierdzania',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    let errorMessage = 'Wystąpił błąd połączenia';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: 'Błąd',
                        text: errorMessage,
                        icon: 'error'
                    });
                }
            });
        }
    });
};

window.odrzucKarte = function(kartaId, nazwaPrzekazujacego, wasteCode, kpoId) {
    Swal.fire({
        title: 'Podaj prawidłową masę',
        html: `
            <div class="mt-2">
                <p class="mb-2" style="font-size: 0.9rem;">${nazwaPrzekazujacego}</p>
                <p class="mb-3 fw-bold" style="font-size: 1.1rem;">${wasteCode}</p>
                <input type="text" id="prawidlowaMasa" class="form-control form-control-lg text-center" placeholder="np. 12,500">
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Odrzuć',
        cancelButtonText: 'Anuluj',
        didOpen: () => {
            document.getElementById('prawidlowaMasa').focus();
        },
        preConfirm: () => {
            const masa = document.getElementById('prawidlowaMasa').value;
            if (!masa) {
                Swal.showValidationMessage('Proszę podać masę');
                return false;
            }
            const masaFormatowana = parseFloat(masa.replace(',', '.')).toFixed(3);
            if (isNaN(masaFormatowana)) {
                Swal.showValidationMessage('Nieprawidłowa wartość');
                return false;
            }
            return masaFormatowana;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Przetwarzanie...',
                text: 'Wysyłanie do BDO',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/biuro/bdo/odrzuc-karte',
                method: 'POST',
                data: {
                    karta_id: kartaId,
                    waste_mass: parseFloat(result.value),
                    kpo_id: kpoId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        Swal.fire({
                            title: 'Odrzucono kartę na masę:',
                            html: `<h3 class="display-6 fw-bold mt-2">${data.waste_mass}</h3>`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Błąd',
                            text: data.message || 'Wystąpił błąd podczas odrzucania',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    let errorMessage = 'Wystąpił błąd połączenia';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: 'Błąd',
                        text: errorMessage,
                        icon: 'error'
                    });
                }
            });
        }
    });
};


window.wygenerujPotwierdzenie = function(kartaId, kpoId, dataPlanowanego) {
    const planowana = new Date(dataPlanowanego.replace(" ", "T"));
    const defaultDate = planowana.toISOString().split("T")[0];
    const defaultTime = planowana.toTimeString().slice(0, 5);

    const minDate = new Date(planowana.getTime() - 24*60*60*1000);
    const maxDate = new Date(planowana.getTime() + 24*60*60*1000);

    const minDateStr = minDate.toISOString().split("T")[0];
    const maxDateStr = maxDate.toISOString().split("T")[0];

    Swal.fire({
        title: 'Potwierdzenie rozpoczęcia transportu',
        width: '700px',
        html: `
            <div class="text-start">
                <label class="fw-bold mb-1">Faktyczna data rozpoczęcia transportu:</label>
                <input type="date" id="dataStart" class="form-control mb-3"
                       value="${defaultDate}" min="${minDateStr}" max="${maxDateStr}">

                <label class="fw-bold mb-1">Faktyczna godzina rozpoczęcia transportu:</label>
                <input type="time" id="czasStart" class="form-control mb-3" value="${defaultTime}">

                <label class="fw-bold mb-1">Numer rejestracyjny środka transportu:</label>
                <input type="text" id="nrTransportu" class="form-control mb-2" placeholder="Wybierz z list poniżej lub wpisz dowolny">

                <div class="row mt-3">
                    <div class="col-6">
                        <div class="border rounded p-2" style="max-height: 260px; overflow-y: auto;">
                            <div class="lewyItem selectable border p-1 mb-1 text-center" data-value="WGM3595C" style="cursor:pointer; font-size: 0.80rem;"><b>WGM3595C</b></div>
                            <div class="lewyItem selectable border p-1 mb-1 text-center" data-value="WGM2624C" style="cursor:pointer; font-size: 0.80rem;">WGM2624C</div>
                            <div class="lewyItem selectable border p-1 mb-1 text-center" data-value="WGM0958F" style="cursor:pointer; font-size: 0.80rem;">WGM0958F</div>
                            <div class="lewyItem selectable border p-1 mb-1 text-center" data-value="PNT81294" style="cursor:pointer; font-size: 0.80rem;">PNT81294</div>
                            <div class="lewyItem selectable border p-1 mb-1 text-center" data-value="ZS438MG" style="cursor:pointer; font-size: 0.80rem;">ZS438MG</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2" style="max-height: 260px; overflow-y: auto;">
                            <div class="prawyItem selectable border p-1 mb-1 text-center" data-value="WGM2125P" style="cursor:pointer; font-size: 0.80rem;"><b>WGM2125P</b></div>
                            <div class="prawyItem selectable border p-1 mb-1 text-center" data-value="PNTKY66" style="cursor:pointer; font-size: 0.80rem;">PNTKY66</div>
                            <div class="prawyItem selectable border p-1 mb-1 text-center" data-value="WGM2126P" style="cursor:pointer; font-size: 0.80rem;">WGM2126P</div>
                            <div class="prawyItem selectable border p-1 mb-1 text-center" data-value="WGM4617P" style="cursor:pointer; font-size: 0.80rem;">WGM4617P</div>
                            <div class="prawyItem selectable border p-1 mb-1 text-center" data-value="WGM5564P" style="cursor:pointer; font-size: 0.80rem;">WGM5564P</div>
                            <div class="prawyItem selectable border p-1 mb-1 text-center" data-value="WGM8340P" style="cursor:pointer; font-size: 0.80rem;">WGM8340P</div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        didOpen: () => {
            const style = document.createElement('style');
            style.innerHTML = `
                .selectedItem {
                    background-color: rgba(0, 123, 255, 0.2) !important;
                    border-color: #007bff;
                }
            `;
            document.head.appendChild(style);

            let selectedLeft = null;
            let selectedRight = null;
            const input = document.getElementById('nrTransportu');

            document.querySelectorAll('.lewyItem').forEach(el => {
                el.addEventListener('click', () => {
                    document.querySelectorAll('.lewyItem').forEach(e => e.classList.remove('selectedItem'));
                    el.classList.add('selectedItem');
                    selectedLeft = el.dataset.value;
                    input.value = selectedRight ? `${selectedLeft}/${selectedRight}` : selectedLeft;
                });
            });

            document.querySelectorAll('.prawyItem').forEach(el => {
                el.addEventListener('click', () => {
                    document.querySelectorAll('.prawyItem').forEach(e => e.classList.remove('selectedItem'));
                    el.classList.add('selectedItem');
                    selectedRight = el.dataset.value;
                    if (selectedLeft) input.value = `${selectedLeft}/${selectedRight}`;
                });
            });
        },
        showCancelButton: true,
        confirmButtonText: 'Zatwierdź',
        cancelButtonText: 'Anuluj',
        preConfirm: () => {
            const dataStart = document.getElementById('dataStart').value;
            const czasStart = document.getElementById('czasStart').value;
            const nrTransportu = document.getElementById('nrTransportu').value.trim();

            if (!dataStart) {
                Swal.showValidationMessage('Podaj datę rozpoczęcia');
                return false;
            }
            if (!czasStart) {
                Swal.showValidationMessage('Podaj godzinę rozpoczęcia');
                return false;
            }
            if (!nrTransportu) {
                Swal.showValidationMessage('Wpisz lub wybierz numer środka transportu');
                return false;
            }

            const wybrana = new Date(`${dataStart}T${czasStart}`);
            const minDateTime = new Date(planowana.getTime() - 24*60*60*1000);
            const maxDateTime = new Date(planowana.getTime() + 24*60*60*1000);

            if (wybrana < minDateTime || wybrana > maxDateTime) {
                Swal.showValidationMessage('Data i godzina muszą być w przedziale ±24h od planowanej');
                return false;
            }

            return {
                data_start: dataStart,
                czas_start: czasStart,
                nr_transportu: nrTransportu
            };
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Przetwarzanie...',
            text: 'Wysyłanie do BDO',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '/biuro/bdo/potwierdz-rozpoczecie',
            method: 'POST',
            data: {
                karta_id: kartaId,
                kpo_id: kpoId,
                data_start: result.value.data_start,
                czas_start: result.value.czas_start,
                nr_transportu: result.value.nr_transportu,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                Swal.fire({
                    title: 'Zatwierdzono',
                    icon: 'success',
                    timer: 1700,
                    showConfirmButton: false
                }).then(() => location.reload());
            },
            error: function(xhr) {
                let errorMessage = 'Wystąpił błąd połączenia';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire('Błąd', errorMessage, 'error');
            }
        });
    });
};


window.aktualizujJednaKarte = function(kartaId, kpoId, calendar_year) {
    Swal.fire({
        title: 'Aktualizuję...',
        text: 'Pobieranie danych z BDO',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/biuro/bdo/aktualizuj-karte',
        method: 'POST',
        data: {
            karta_id: kartaId,
            kpo_id: kpoId,
            calendar_year: calendar_year,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                Swal.fire({
                    title: 'Zaktualizowano',
                    icon: 'success',
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    title: 'Błąd',
                    text: data.message || 'Wystąpił błąd podczas aktualizacji',
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            let errorMessage = 'Wystąpił błąd połączenia';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
                title: 'Błąd',
                text: errorMessage,
                icon: 'error'
            });
        }
    });
};


// Funkcja synchronizacji BDO (dla nawigacji)
window.bdoSync = function(typ) {
    const url = typ === 'przekazujacy' 
        ? '/biuro/bdo/sync-przekazujacy' 
        : '/biuro/bdo/sync';
    
    const title = typ === 'przekazujacy' 
        ? 'Synchronizacja BDO (Przekazujący)' 
        : 'Synchronizacja BDO (Przejmujący)';

    Swal.fire({
        title: title,
        html: 'Trwa pobieranie kart z API BDO...<br><small class="text-muted">To może potrwać kilka minut</small>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                Swal.fire({
                    title: 'Synchronizacja zakończona',
                    html: `
                        <div class="text-start">
                            <p><strong>Pobrano:</strong> ${data.total}</p>
                            <p><strong>Utworzono:</strong> ${data.created}</p>
                            <p><strong>Zaktualizowano:</strong> ${data.updated}</p>
                            <p><strong>Pominięto:</strong> ${data.skipped}</p>
                            <p><strong>Błędy:</strong> ${data.errors}</p>
                        </div>
                    `,
                    icon: 'success'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    title: 'Błąd synchronizacji',
                    text: data.message || 'Wystąpił błąd',
                    icon: 'error'
                });
            }
        },
        error: function(xhr) {
            let errorMessage = 'Wystąpił błąd połączenia';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire({
                title: 'Błąd',
                text: errorMessage,
                icon: 'error'
            });
        }
    });
};
