import axios from 'axios'

let token = 'f173af42772a2e3917694f26bd756c90';

export default() => {
    return axios.create({
        baseURL: `http://vex-astrafarma.test/api/v1`,
        //baseURL: `http://astrafarma.vexmobile.com.br/api/v1`,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'request-ajax': 'Token '+ token
        }
    })
}
