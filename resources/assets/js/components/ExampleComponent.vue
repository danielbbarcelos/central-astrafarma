<script>
    export default {
        template: require('./exampleComponent.html'),
        components:{
           //Aqui vão outros componentes
        },
        data:  function () {
            return {
               //Aqui vão as variáveis
            }
        },
        methods: {
           // Aqui vão os métodos
        },
        computed: {
            // Aqui vão os dados computados (variáveis carregadas antes do carregamento da tela - Ex: atribui retornos da API para variáveis)
        },

        created () {
            // Métodos disparados na criação do componente (Ex: Chamadas na API)
        },

        mounted: function () {
            // Métodos disparados quando o componente foi montado (vide https://br.vuejs.org/v2/guide/instance.html)
            console.log("Componente montado!")
        }
    }
</script>
