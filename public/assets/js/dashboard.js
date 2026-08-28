/**
 * Filtros do dashboard via AJAX (fetch).
 * Ao enviar o formulário de filtros, busca os serviços em
 * servicos_filtrar.php (JSON) e re-renderiza só a tabela,
 * sem recarregar a página inteira.
 */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('filtros-form');
    var tbody = document.getElementById('tabela-servicos');

    if (!form || !tbody) {
        return;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        buscarServicos();
    });

    function buscarServicos() {
        var params = new URLSearchParams(new FormData(form));

        // atualiza a URL (sem recarregar) pra manter os filtros ao dar F5
        var novaUrl = window.location.pathname + '?' + params.toString();
        window.history.replaceState(null, '', novaUrl);

        mostrarCarregando();

        fetch('servicos_filtrar.php?' + params.toString())
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Falha ao buscar serviços');
                }
                return response.json();
            })
            .then(renderizarTabela)
            .catch(function () {
                tbody.innerHTML = '<tr><td colspan="6" class="empty">Não foi possível carregar os serviços. Tente novamente.</td></tr>';
            });
    }

    function mostrarCarregando() {
        tbody.innerHTML = '<tr><td colspan="6" class="empty">Carregando...</td></tr>';
    }

    function renderizarTabela(servicos) {
        if (!servicos.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="empty">Nenhum serviço encontrado.</td></tr>';
            return;
        }

        tbody.innerHTML = servicos.map(criarLinha).join('');
    }

    function criarLinha(servico) {
        var finalizado = !!servico.finished_at;
        var statusClasse = finalizado ? 'finalizado' : 'pendente';
        var statusTexto = finalizado ? 'Finalizado' : 'Pendente';

        var comissaoHtml = '';
        if (finalizado) {
            comissaoHtml = '<div style="font-size:12px; color:#666; margin-top:4px;">Comissão: '
                + formatarMoeda(servico.commission_user) + '</div>';
        }

        var botaoFinalizar = '';
        if (!finalizado) {
            botaoFinalizar = ''
                + '<form method="POST" action="servico_finalizar.php" onsubmit="return confirm(\'Finalizar este serviço? Essa ação não pode ser desfeita.\');">'
                + '<input type="hidden" name="id" value="' + servico.id_service + '">'
                + '<button type="submit">Finalizar</button>'
                + '</form>';
        }

        return ''
            + '<tr>'
            + '<td>' + servico.id_service + '</td>'
            + '<td>' + escapeHtml(servico.description) + '</td>'
            + '<td>' + formatarMoeda(servico.price) + '</td>'
            + '<td><span class="status ' + statusClasse + '">' + statusTexto + '</span>' + comissaoHtml + '</td>'
            + '<td>' + escapeHtml(servico.user_name) + '</td>'
            + '<td class="actions">'
            + '<a href="servico_editar.php?id=' + servico.id_service + '">Alterar</a>'
            + '<form method="POST" action="servico_excluir.php" onsubmit="return confirm(\'Excluir este serviço?\');">'
            + '<input type="hidden" name="id" value="' + servico.id_service + '">'
            + '<button type="submit" class="delete">Excluir</button>'
            + '</form>'
            + botaoFinalizar
            + '</td>'
            + '</tr>';
    }

    function formatarMoeda(valor) {
        var numero = parseFloat(valor || 0);
        return 'R$ ' + numero.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function escapeHtml(texto) {
        var div = document.createElement('div');
        div.textContent = texto || '';
        return div.innerHTML;
    }
});