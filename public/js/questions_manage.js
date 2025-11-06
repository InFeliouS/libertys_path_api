(() => {
  const tbody = document.getElementById("qTbody");
  const form = document.getElementById("qForm");
  const formTitle = document.getElementById("formTitle");
  const submitBtn = document.getElementById("submitBtn");
  const cancelEditBtn = document.getElementById("cancelEditBtn");
  const formMsg = document.getElementById("formMsg");

  const f = {
    id: document.getElementById("qId"),
    question_text: document.getElementById("question_text"),
    choice1: document.getElementById("choice1"), // A
    choice2: document.getElementById("choice2"), // B
    choice3: document.getElementById("choice3"), // C
    choice4: document.getElementById("choice4"), // D
    correct_index: document.getElementById("correct_index"),
  };

  const API_BASE = "api/v1/guard_questions";
  const letters = ["A", "B", "C", "D"];
  const idxToLetter = (i) => letters[i] ?? "";
  const html = (s) =>
    String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");

  function fmtChoices(row) {
    const arr = [row.choice1, row.choice2, row.choice3, row.choice4];
    return arr.map((c, i) => `<div><b>${idxToLetter(i)}</b>: ${html(c)}</div>`).join("");
  }

  function correctDisplay(row) {
    const i = Number(row.correct_index);
    if (!Number.isInteger(i) || i < 0 || i > 3) return "";
    const text = [row.choice1, row.choice2, row.choice3, row.choice4][i] ?? "";
    return `${idxToLetter(i)}: ${html(text)}`;
  }

  async function loadQuestions() {
    tbody.innerHTML = `<tr><td colspan="4">Loading…</td></tr>`;
    try {
      const res = await fetch(`${API_BASE}/list.php`, { credentials: "same-origin" });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || data?.ok !== true || !Array.isArray(data.items)) {
        throw new Error(data?.error || "Failed to load.");
      }

      if (data.items.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4">No questions found.</td></tr>`;
        return;
      }

      tbody.innerHTML = data.items
        .map((row) => {
          return `
            <tr data-id="${row.id}">
              <td>${html(row.question_text)}</td>
              <td>${fmtChoices(row)}</td>
              <td>${correctDisplay(row)}</td>
              <td class="row-actions">
                <button data-action="edit" class="edit" data-id="${row.id}">EDIT</button>
                <button data-action="delete" data-id="${row.id}" class="btn-ghost">DELETE</button>
              </td>
            </tr>
          `;
        })
        .join("");
    } catch (err) {
      console.error(err);
      tbody.innerHTML = `<tr><td colspan="4">Failed to load.</td></tr>`;
    }
  }

  function clearForm() {
    f.id.value = "";
    f.question_text.value = "";
    f.choice1.value = "";
    f.choice2.value = "";
    f.choice3.value = "";
    f.choice4.value = "";
    f.correct_index.value = "";
    formTitle.textContent = "ADD QUESTION";
    submitBtn.textContent = "CREATE QUESTION";
    cancelEditBtn.classList.add("hidden");
    formMsg.textContent = "";
  }

  function fillForm(row) {
    f.id.value = row.id;
    f.question_text.value = row.question_text ?? "";
    f.choice1.value = row.choice1 ?? "";
    f.choice2.value = row.choice2 ?? "";
    f.choice3.value = row.choice3 ?? "";
    f.choice4.value = row.choice4 ?? "";
    f.correct_index.value = String(row.correct_index ?? "");
    formTitle.textContent = "EDIT QUESTION";
    submitBtn.textContent = "SAVE CHANGES";
    cancelEditBtn.classList.remove("hidden");
    formMsg.textContent = "";
  }

  async function createQuestion(payload) {
    const res = await fetch(`${API_BASE}/create.php`, {
      method: "POST",
      body: payload,
      credentials: "same-origin",
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.ok !== true) throw new Error(data?.error || "Create failed");
    return data;
  }

  async function updateQuestion(payload) {
    const res = await fetch(`${API_BASE}/update.php`, {
      method: "POST",
      body: payload,
      credentials: "same-origin",
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.ok !== true) throw new Error(data?.error || "Update failed");
    return data;
  }

  async function deleteQuestion(id) {
    const fd = new FormData();
    fd.set("id", String(id));
    const res = await fetch(`${API_BASE}/delete.php`, {
      method: "POST",
      body: fd,
      credentials: "same-origin",
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.ok !== true) throw new Error(data?.error || "Delete failed");
    return data;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    formMsg.textContent = "";

    const id = f.id.value.trim();
    const fd = new FormData();
    if (id) fd.set("id", id);
    fd.set("question_text", f.question_text.value.trim());
    fd.set("choice1", f.choice1.value.trim());
    fd.set("choice2", f.choice2.value.trim());
    fd.set("choice3", f.choice3.value.trim());
    fd.set("choice4", f.choice4.value.trim());
    fd.set("correct_index", String(f.correct_index.value));

    try {
      submitBtn.disabled = true;
      if (id) { await updateQuestion(fd); formMsg.textContent = "Saved."; }
      else    { await createQuestion(fd); formMsg.textContent = "Created."; }
      clearForm();
      await loadQuestions();
    } catch (err) {
      console.error(err);
      formMsg.textContent = err?.message || "Error.";
    } finally {
      submitBtn.disabled = false;
    }
  });

  cancelEditBtn.addEventListener("click", () => clearForm());

  tbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const action = btn.getAttribute("data-action");
    const id = btn.getAttribute("data-id");
    if (!action || !id) return;

    if (action === "edit") {
      try {
        const res = await fetch(`${API_BASE}/list.php`, { credentials: "same-origin" });
        const data = await res.json().catch(() => ({}));
        const row = (data.items || []).find((r) => String(r.id) === String(id));
        if (row) fillForm(row);
      } catch (_) {}
    }

    if (action === "delete") {
      if (!confirm("Delete this question?")) return;
      try {
        btn.disabled = true;
        await deleteQuestion(id);
        await loadQuestions();
      } catch (err) {
        console.error(err);
        alert(err?.message || "Delete failed");
      } finally {
        btn.disabled = false;
      }
    }
  });

  clearForm();
  loadQuestions();
})();

// Bounce animation when number changes
["roomCount", "enemyCount"].forEach(id => {
  const el = document.getElementById(id);
  el.addEventListener("input", () => {
    el.classList.remove("value-bounce");
    void el.offsetWidth; // trigger reflow
    el.classList.add("value-bounce");
  });
});

// Change border color based on difficulty
const radios = document.querySelectorAll('input[name="difficulty"]');
const formCard = document.querySelector(".card");

radios.forEach(radio => {
  radio.addEventListener("change", () => {
    formCard.classList.remove("easy-active", "medium-active", "hard-active");
    formCard.classList.add(radio.value + "-active");
  });
});
