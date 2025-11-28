document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.getElementById('cpf');
    const cepInput = document.getElementById('cep');
    const telInput = document.getElementById('telefone');
    const emailInput = document.getElementById('email');

    if (cpfInput) {
        cpfInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });

        cpfInput.addEventListener('blur', function() {
            if (this.value !== "" && !validarCPF(this.value)) {
                mostrarErroNaTela('CPF Inválido. Verifique os números digitados.');
                this.value = ""; 
                this.focus();
            }
        });
    }

    if (cepInput) {
        cepInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });

        cepInput.addEventListener('blur', function() {
            validarCEP(this.value);
        });
    }

    if (telInput) {
        telInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            if (value.length > 13) {
                value = value.replace(/(\d{5})(\d{4})$/, '$1-$2');
            } else {
                value = value.replace(/(\d{4})(\d{4})$/, '$1-$2');
            }
            e.target.value = value;
        });
    }

    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value !== "" && !validarEmail(this.value)) {
                mostrarErroNaTela('Formato de e-mail inválido.');
            }
        });
    }
});

function mostrarErroNaTela(mensagem) {
    const msgAntiga = document.querySelector('.message.js-error');
    if (msgAntiga) msgAntiga.remove();

    const divErro = document.createElement('div');
    divErro.className = 'message error js-error';
    divErro.innerText = mensagem;

    const container = document.querySelector('.container');
    const titulo = container.querySelector('h2');
    
    if (titulo) {
        container.insertBefore(divErro, titulo.nextSibling);
    } else {
        container.prepend(divErro);
    }

    setTimeout(() => {
        divErro.remove();
    }, 5000);
}

function mostrarSucessoNaTela(mensagem) {
    const msgAntiga = document.querySelector('.message.js-error');
    if (msgAntiga) msgAntiga.remove();

    const divSucesso = document.createElement('div');
    divSucesso.className = 'message success js-error';
    divSucesso.innerText = mensagem;

    const container = document.querySelector('.container');
    const titulo = container.querySelector('h2');
    
    if (titulo) container.insertBefore(divSucesso, titulo.nextSibling);
    else container.prepend(divSucesso);

    setTimeout(() => { divSucesso.remove(); }, 4000);
}

function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf == '') return false;
    if (cpf.length != 11 || /^(\d)\1{10}$/.test(cpf)) return false;
    let add = 0;
    for (let i = 0; i < 9; i++) add += parseInt(cpf.charAt(i)) * (10 - i);
    let rev = 11 - (add % 11);
    if (rev == 10 || rev == 11) rev = 0;
    if (rev != parseInt(cpf.charAt(9))) return false;
    add = 0;
    for (let i = 0; i < 10; i++) add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11) rev = 0;
    if (rev != parseInt(cpf.charAt(10))) return false;
    return true;
}

function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validarCEP(cep) {
    var cepLimpo = cep.replace(/\D/g, '');
    if (cepLimpo !== "") {
        var validacep = /^[0-9]{8}$/;
        if(validacep.test(cepLimpo)) {
            
            fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        mostrarErroNaTela('CEP não encontrado na base dos Correios.');
                        document.getElementById('cep').value = "";
                    } else {
                        mostrarSucessoNaTela('CEP Localizado: ' + data.localidade + '/' + data.uf);
                    }
                })
                .catch(error => {
                    mostrarErroNaTela('Erro ao buscar CEP (Sem internet?).');
                });
        } else {
            mostrarErroNaTela('Formato de CEP inválido.');
            document.getElementById('cep').value = "";
        }
    }
}