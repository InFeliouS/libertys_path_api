/* public/js/teacher_config.js
   Enforces:
     - roomCount: 1..8
     - enemyCount: 1..roomCount  (cannot exceed rooms)
   Shows inline error + alert and prevents POST when invalid.
*/
document.addEventListener('DOMContentLoaded', () => {
  const saveBtn = document.getElementById('metaSaveBtn');
  const roomInput = document.getElementById('roomCount');
  const enemyInput = document.getElementById('enemyCount');
  const radios = document.querySelectorAll('input[name="difficulty"]');
  if (!saveBtn || !roomInput || !enemyInput || radios.length === 0) return;

  // ensure min/max attributes are set (helps UI)
  roomInput.setAttribute('min', '1');
  roomInput.setAttribute('max', '8');
  enemyInput.setAttribute('min', '1');

  // status element
  let status = document.getElementById('metaSaveStatus');
  if (!status) {
    status = document.createElement('span');
    status.id = 'metaSaveStatus';
    status.style.marginLeft = '12px';
    status.style.fontSize = '1.2rem';
    saveBtn.insertAdjacentElement('afterend', status);
  }

  // helper clamp
  const clamp = (v, min, max) => {
    const n = parseInt(v, 10);
    if (Number.isNaN(n)) return min;
    return Math.min(max, Math.max(min, n));
  };

  // map numeric difficulty to radio UI
  const setDifficultyUI = (val) => {
    const map = ['easy', 'medium', 'hard'];
    const label = map[val] || 'medium';
    const radio = document.querySelector(`input[name="difficulty"][value="${label}"]`);
    if (radio) radio.checked = true;
  };
  const getDifficultyValue = () => {
    const diffNode = document.querySelector('input[name="difficulty"]:checked');
    switch (diffNode ? diffNode.value : 'medium') {
      case 'easy': return 0;
      case 'medium': return 1;
      case 'hard': return 2;
      default: return 1;
    }
  };

  // Live validation: ensure enemy max updates when rooms change
  function validateAndUpdateUI() {
    // coerce ints
    const rooms = clamp(roomInput.value, 1, 8);
    roomInput.value = rooms;

    // enemy must be >=1 and <= rooms
    const enemy = clamp(enemyInput.value, 1, rooms);
    enemyInput.max = rooms; // useful for number spinners
    enemyInput.value = enemy;
  }

  // On input change, do live constraint forcing (helps UX)
  roomInput.addEventListener('input', () => {
    // if teacher types >8 or <1, clamp visually
    const v = parseInt(roomInput.value, 10);
    if (Number.isNaN(v)) return;
    if (v < 1) roomInput.value = 1;
    if (v > 8) roomInput.value = 8;
    // update enemy upper bound and clamp enemy
    const rooms = clamp(roomInput.value, 1, 8);
    if (parseInt(enemyInput.value, 10) > rooms) enemyInput.value = rooms;
  });

  enemyInput.addEventListener('input', () => {
    const rooms = clamp(roomInput.value, 1, 8);
    let e = parseInt(enemyInput.value, 10);
    if (Number.isNaN(e)) return;
    if (e < 1) enemyInput.value = 1;
    if (e > rooms) enemyInput.value = rooms;
  });

  // Load saved config from server
  (async function loadConfig() {
    try {
      const resp = await fetch('index.php?r=api/v1/teacher_config', { method: 'GET', credentials: 'same-origin' });
      const j = await resp.json();
      console.log('GET teacher_config', j);
      if (resp.ok && j.success && j.data) {
        const d = j.data;
        if (typeof d.room_count !== 'undefined') roomInput.value = d.room_count;
        if (typeof d.enemy_count !== 'undefined') enemyInput.value = d.enemy_count;
        if (typeof d.difficulty !== 'undefined') setDifficultyUI(parseInt(d.difficulty));
      }
    } catch (e) {
      console.warn('Load config failed', e);
    }
  })();

  // Validate before sending — returns null if OK, or an error message string
  function validateBeforeSave(payload) {
    if (!Number.isInteger(payload.roomCount) || payload.roomCount < 1 || payload.roomCount > 8) {
      return 'Rooms must be an integer between 1 and 8.';
    }
    if (!Number.isInteger(payload.enemyCount) || payload.enemyCount < 1) {
      return 'Enemies must be an integer of at least 1.';
    }
    if (payload.enemyCount > payload.roomCount) {
      return 'Enemy count must not exceed the room count.';
    }
    return null;
  }

  // Save handler
  saveBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    // prepare payload
    const payload = {
      roomCount: clamp(roomInput.value, 1, 8),
      enemyCount: clamp(enemyInput.value, 1, clamp(roomInput.value, 1, 8)),
      difficulty: getDifficultyValue()
    };

    // run validation
    const validationError = validateBeforeSave(payload);
    if (validationError) {
      // show inline error + alert and stop
      status.textContent = '❌ ' + validationError;
      status.style.color = '#b33';
      // make it more visible
      window.alert(validationError);
      return;
    }

    // UI feedback
    saveBtn.disabled = true;
    status.textContent = 'Saving...';
    status.style.color = '#444';

    try {
      const resp = await fetch('index.php?r=api/v1/teacher_config', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        credentials: 'same-origin'
      });
      const j = await resp.json();
      console.log('POST teacher_config', j);

      if (resp.ok && j.success) {
        status.textContent = '✅ Saved';
        status.style.color = '#0a0';
      } else {
        // server-side validation message or generic
        const errMsg = (j && (j.error || j.message)) ? (j.error || j.message) : 'Save failed';
        status.textContent = '❌ ' + errMsg;
        status.style.color = '#b33';
        window.alert(errMsg);
      }
    } catch (err) {
      console.error('POST failed', err);
      status.textContent = '❌ Network error';
      status.style.color = '#b33';
      window.alert('Network error — could not save configuration.');
    } finally {
      saveBtn.disabled = false;
      setTimeout(() => { status.textContent = ''; }, 3000);
    }
  });
});
