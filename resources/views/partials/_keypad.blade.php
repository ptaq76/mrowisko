{{-- Numpad bottom-sheet: opt-in przez class="js-numkey" na <input> --}}
<style>
    #keypadOverlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.55);
        z-index: 9998;
        opacity: 0; pointer-events: none;
        transition: opacity .18s ease;
    }
    #keypadOverlay.open { opacity: 1; pointer-events: auto; }

    #keypadSheet {
        position: fixed; left: 0; right: 0; bottom: 0;
        z-index: 9999;
        background: #1a1a1a;
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
        padding: 14px 14px calc(14px + env(safe-area-inset-bottom, 0px));
        box-shadow: 0 -8px 28px rgba(0,0,0,.45);
        transform: translateY(110%);
        transition: transform .25s cubic-bezier(.4,0,.2,1);
        max-width: 480px; margin: 0 auto;
        font-family: 'Barlow', sans-serif;
        user-select: none; -webkit-user-select: none;
    }
    #keypadSheet.open { transform: translateY(0); }

    #keypadSheet .kp-handle {
        width: 38px; height: 4px;
        background: #444; border-radius: 2px;
        margin: 0 auto 10px;
    }

    #keypadSheet .kp-display-wrap {
        background: #000;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 12px;
        text-align: right;
        min-height: 78px;
        display: flex; flex-direction: column; justify-content: center;
    }
    #keypadSheet .kp-label {
        font-size: 12px; color: #888;
        font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase;
        text-align: left;
        min-height: 14px;
    }
    #keypadSheet .kp-display {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 44px; font-weight: 900; color: #F5C842;
        line-height: 1.05;
        word-break: break-all;
    }
    #keypadSheet .kp-error {
        color: #e74c3c; font-size: 12px; font-weight: 700;
        text-align: left; min-height: 14px;
        letter-spacing: .04em;
    }

    #keypadSheet .kp-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        margin-bottom: 10px;
    }
    #keypadSheet .kp-grid button {
        height: 64px;
        background: #2a2a2a;
        border: 1px solid #3a3a3a;
        color: #fff;
        font-family: 'Barlow', sans-serif;
        font-size: 26px; font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: background .08s ease, transform .08s ease;
        -webkit-tap-highlight-color: transparent;
    }
    #keypadSheet .kp-grid button:active {
        background: #F5C842;
        color: #1a1a1a;
        transform: scale(.97);
    }
    #keypadSheet .kp-grid button.kp-del {
        background: #3a1f1f;
        border-color: #5a2a2a;
        color: #ff8a80;
        font-size: 22px;
    }
    #keypadSheet .kp-grid button.kp-del:active {
        background: #c0392b;
        color: #fff;
    }

    #keypadSheet .kp-actions {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 8px;
    }
    #keypadSheet .kp-actions button {
        height: 56px;
        border: none; border-radius: 12px;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 20px; font-weight: 900;
        letter-spacing: .08em; text-transform: uppercase;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }
    #keypadSheet .kp-cancel {
        background: #2a2a2a; color: #aaa;
    }
    #keypadSheet .kp-cancel:active { background: #444; color: #fff; }
    #keypadSheet .kp-save {
        background: #F5C842; color: #1a1a1a;
    }
    #keypadSheet .kp-save:active { background: #d4a800; }

    /* Pola .js-numkey — wizualny sygnał, że to "klikalne" */
    .js-numkey {
        cursor: pointer !important;
        caret-color: transparent;
    }
</style>

<div id="keypadOverlay"></div>

<div id="keypadSheet" role="dialog" aria-label="Klawiatura numeryczna">
    <div class="kp-handle"></div>

    <div class="kp-display-wrap">
        <div class="kp-label" id="keypadLabel"></div>
        <div class="kp-display" id="keypadDisplay">0</div>
        <div class="kp-error" id="keypadError"></div>
    </div>

    <div class="kp-grid">
        <button type="button" data-key="1">1</button>
        <button type="button" data-key="2">2</button>
        <button type="button" data-key="3">3</button>
        <button type="button" data-key="4">4</button>
        <button type="button" data-key="5">5</button>
        <button type="button" data-key="6">6</button>
        <button type="button" data-key="7">7</button>
        <button type="button" data-key="8">8</button>
        <button type="button" data-key="9">9</button>
        <button type="button" data-key=".">.</button>
        <button type="button" data-key="0">0</button>
        <button type="button" data-key="del" class="kp-del" aria-label="Backspace">
            <i class="fas fa-backspace"></i>
        </button>
    </div>

    <div class="kp-actions">
        <button type="button" id="keypadCancel" class="kp-cancel">Anuluj</button>
        <button type="button" id="keypadSave"   class="kp-save">Zapisz</button>
    </div>
</div>

<script src="{{ asset('js/keypad.js') }}?v={{ filemtime(public_path('js/keypad.js')) }}"></script>
