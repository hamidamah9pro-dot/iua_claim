document.addEventListener('DOMContentLoaded', () => {
    chargerReclamations();

    document.getElementById('logoutBtn').addEventListener('click', () => {
        // Logique de déconnexion ici
        window.location.href = 'login.html';
    });
});

async function chargerReclamations() {
    const tbody = document.getElementById('reclamation_body');
    
    try {
        const res = await fetch('api/prof/get_reclamations.php', { credentials: 'include' });
        const data = await res.json();

        if (data.error) {
            tbody.innerHTML = `<tr><td colspan="7">Erreur : ${data.error}</td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        let pendingCount = 0;

        data.forEach(r => {
            if(r.statut === 'EN_ATTENTE') pendingCount++;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${r.nom} ${r.prenom}</strong></td>
                <td>${r.matiere}</td>
                <td>${r.note_actuelle} / 20</td>
                <td><span class="highlight">${r.note_reclamee} / 20</span></td>
                <td><div class="motif-text">${r.motif}</div></td>
                <td><span class="badge ${r.statut.toLowerCase()}">${r.statut}</span></td>
                <td class="actions">
                    ${r.statut === 'EN_ATTENTE' ? `
                        <button onclick="changerStatut(${r.id}, 'ACCEPTEE')" class="btn-check" title="Accepter"><i class="fa-solid fa-check"></i></button>
                        <button onclick="changerStatut(${r.id}, 'REFUSEE')" class="btn-x" title="Refuser"><i class="fa-solid fa-xmark"></i></button>
                    ` : `<i class="fa-solid fa-lock" title="Traité"></i>`}
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('count_total').textContent = data.length;
        document.getElementById('count_pending').textContent = pendingCount;

    } catch (err) {
        console.error("Erreur chargement:", err);
    }
}

async function changerStatut(id, nouveauStatut) {
    if(!confirm(`Confirmer le statut : ${nouveauStatut} ?`)) return;

    try {
        const res = await fetch('api/reclamations/update_statut.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, statut: nouveauStatut })
        });

        const result = await res.json();
        if (result.success) {
            chargerReclamations(); // Rafraîchir la liste
        } else {
            alert("Erreur lors de la mise à jour.");
        }
    } catch (err) {
        alert("Erreur de connexion au serveur.");
    }
}