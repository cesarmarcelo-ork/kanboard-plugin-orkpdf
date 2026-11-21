> OrkPdf 0.2.7-alpha - Portuguese/English reporting and corrected print/PDF styling on Kanboard 1.2.50 and 1.2.57.

# OrkPdf 0.2.7-alpha

Plugin nativo de relatórios do Kanboard. Extensão K-OrkFlowTech original permanece independente.

## Implementado
- Menu lateral do Painel com ícone de impressora; atalho no menu de projetos.
- Formulário com envio nativo GET para a rota de relatórios; não depende de `window.location.assign`.
- Cartão detalhado por ID e inventário de cartões por quadro.
- Seleção de campos do cartão, reordenação por arrastar, habilitação de descrição/subtarefas/comentários.
- Prévia **esquemática** e personalização de título, orientação, densidade e exibição da data.
- Última configuração salva automaticamente no navegador, sem seletor de modelos.
- Relatório real usando os dados lidos no backend e botão imprimir/salvar PDF.

## Limitações conhecidas
- Prévia da configuração é esquemática, não fiel à paginação de um documento real.
- Campos extras e nomes de vários identificadores ainda não são enriquecidos; algumas informações aparecem como ID.
- Não há seleção visual de cartões por raia/coluna; logo personalizada, inventário de quadros, renderização rica de Markdown, ordenação de duas tabelas e equivalência visual integral da extensão ainda pendentes.
- Somente leitura. A versão 0.2.7-alpha foi validada funcionalmente pelo mantenedor no Kanboard 1.2.50 e 1.2.57; mantenha backups antes de atualizar.

## Instalação
- Extraia ZIP em diretório temporário; confirme `OrkPdf/Plugin.php`.
- **Não faça backup dentro do diretório `plugins`**, pois o Kanboard tenta carregar cada diretório como plugin.
- Faça backup em `/opt/orkflowtech/kanboard/backups-orkpdf` e substitua a pasta OrkPdf.
- Se fizer a instalação no host, use UID/GID numéricos obtidos por `docker exec kanboard id -u nginx` e `id -g nginx`, pois a conta nginx pode não existir no host.
- Validar sintaxe PHP, JS e teste de renderização com `bash tests/check.sh`.

## Próximos testes integrados
1. Verificar a existência do menu OrkPdf e ausência de HTTP 500.
2. Selecionar quadro e ID de cartão existente e clicar `Visualizar relatório real`.
3. Confirmar que a URL do relatório contém o ID do projeto correto, e que o PDF mostra os dados.
4. Desmarcar descrição e confirmar que desaparece da versão impressa.
5. Mover campo no editor e confirmar sua posição no relatório.
6. Verificar inventário e imprimir/salvar PDF.
7. Validar bloqueio de projetos sem permissão com usuário restrito.

Publicação: veja os releases do repositório oficial. Esta versão continua alpha e não tem equivalência integral com a extensão.

## 0.2.1-alpha
- Exibição do nome completo do Designado e do Criador no relatório, com fallback para o nome de usuário.
- Sem responsável: Não designado. Usuário sem cadastro: Usuário indisponível.

## 0.2.2-alpha
- Removida a seção Modelo local e os controles Salvar modelo/Excluir.
- Mantida a restauração automática da última configuração utilizada no navegador.
- Sem alterações no controlador, na estrutura dos relatórios ou nas permissões.

## 0.2.3-alpha
- Padronizados os campos de texto, número e seleção do formulário: largura integral, 40 px de altura, bordas, fontes e foco consistentes.
- Ocultados os controles de incremento do ID numérico sem alterar sua validação.
- Layout responsivo para orientação e densidade; sem alterações na lógica de geração de PDFs.

## 0.2.5-alpha
- Removida a referência "OrkPdf · Kanboard" do cabeçalho do PDF e da prévia esquemática.
- Rodapé de ambos os relatórios (detalhado e inventário) e da prévia: "Gerado com OrkPdf | OrkFlowTech" e hyperlink www.orkflowtech.com.br.
- Mantidos ícone de impressora, título personalizado, quadro e data de emissão.
- Nenhuma mudança nos dados, filtros, campos ou permissões.

## Internationalization / Internacionalização (0.2.5-alpha)

- Locale/en_US and Locale/pt_BR contain Kanboard translations.
- Plugin::onStartup() loads translations using the active Kanboard language.
- PHP templates use English translation keys; JavaScript preview receives translated values via escaped JSON data.
- English fallback for unsupported languages; no separate language control.
- Existing browser layout preferences are preserved.

## v0.2.7-alpha - Print fidelity

The PDF report now embeds its own scoped print styling so browser print/save output retains the same tables, typography, spacing, and footer as the on-screen report, even when Kanboard supplies theme assets only for screen media. Kanboard navigation is suppressed when printing.
