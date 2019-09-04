import Api from "./api";

export default {

    // Clientes
    getClientes(){
        return Api().get('/clientes');
    },

    // Condições de pagamento
    getCondicoesPagamento(){
        return Api().get('/condicoes-pagamentos');
    },

    // Tabelas de preços
    getTabelasPrecos(){
        return Api().get('/tabelas-precos');
    },

    // Produtos
    getProdutos(){
        return Api().get('/produtos');
    },

    //Adiciona Pedido
    addPedido(data){
        return Api().post('/pedidos/add',data);
    }
}
