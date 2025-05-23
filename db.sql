-- Tabela de Tipos de Gabinetes
CREATE TABLE
  gabinete_tipo (
    gabinete_tipo_id varchar(36) NOT NULL,
    gabinete_tipo_nome varchar(255) NOT NULL UNIQUE,
    gabinete_tipo_informacoes TEXT NULL,
    gabinete_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (gabinete_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Tabela de Gabinetes
CREATE TABLE
  gabinete (
    gabinete_id varchar(36) NOT NULL,
    gabinete_tipo varchar(36) NOT NULL,
    gabinete_nome varchar(50) NOT NULL,
    gabinete_usuarios varchar(50) NOT NULL,
    gabinete_estado varchar(50) NOT NULL,
    gabinete_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    gabinete_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (gabinete_id),
    CONSTRAINT fk_gabinete_tipo FOREIGN KEY (gabinete_tipo) REFERENCES gabinete_tipo (gabinete_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Tabela de Tipos de Usuários
CREATE TABLE
  usuario_tipo (
    usuario_tipo_id varchar(36) NOT NULL,
    usuario_tipo_nome varchar(255) NOT NULL,
    usuario_tipo_descricao varchar(255) NOT NULL,
    PRIMARY KEY (usuario_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Tabela de Usuários
CREATE TABLE
  usuario (
    usuario_id varchar(36) NOT NULL,
    usuario_tipo varchar(36) NOT NULL,
    usuario_gabinete varchar(36) NOT NULL,
    usuario_nome varchar(255) NOT NULL,
    usuario_email varchar(255) NOT NULL UNIQUE,
    usuario_aniversario DATE DEFAULT NULL,
    usuario_telefone varchar(20) NOT NULL,
    usuario_senha varchar(255) NOT NULL,
    usuario_token varchar(36) DEFAULT NULL,
    usuario_foto text DEFAULT NULL,
    usuario_ativo tinyint NOT NULL,
    usuario_gestor BOOLEAN NOT NULL DEFAULT FALSE,
    usuario_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id),
    CONSTRAINT fk_usuario_tipo FOREIGN KEY (usuario_tipo) REFERENCES usuario_tipo (usuario_tipo_id),
    CONSTRAINT fk_usuario_gabinete FOREIGN KEY (usuario_gabinete) REFERENCES gabinete (gabinete_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


INSERT INTO gabinete_tipo (gabinete_tipo_id, gabinete_tipo_nome, gabinete_tipo_informacoes)
  VALUES
    (1, 'Outro', 'Gabinete administrativo'),
    (2, 'Deputado Federal', 'Gabinete destinado a um deputado federal no Congresso Nacional'),
    (3, 'Deputado Estadual', 'Gabinete destinado a um deputado estadual nas assembleias estaduais'),
    (4, 'Vereador', 'Gabinete destinado a um vereador nas câmaras municipais'),
    (5, 'Prefeito', 'Gabinete destinado ao prefeito de um município'),
    (6, 'Governador', 'Gabinete destinado ao governador de um estado'),
    (7, 'Senador', 'Gabinete destinado a um senador no Senado Federal'), 
    (8, 'Secretaria de municipio', 'Secretaria de um munícipio'),
    (9, 'Secretaria de estado', 'Secretaria de um estado');  

INSERT INTO gabinete (gabinete_id, gabinete_tipo, gabinete_nome, gabinete_usuarios, gabinete_estado) VALUES ('1', '1', 'GABINETE_SISTEMA', '1', 'SP');

INSERT INTO usuario_tipo (usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) 
  VALUES 
    (1, 'Administrador', 'Usuario root do sistema'),
    (2, 'Administrativo', 'Usuario administrativo'),
    (3, 'Comunicação', 'Usuario da assessoria de comunicação'),
    (4, 'Legislativo', 'Usuario da assessoria legislativa'),
    (5, 'Orçamento', 'Usuario da assessoria orçamentária'),
    (6, 'Secretaria', 'Usuario da secretaria do gabinete');  

INSERT INTO usuario (usuario_id, usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_tipo, usuario_ativo, usuario_aniversario, usuario_gabinete) 
VALUES ('1', 'USUÁRIO SISTEMA', 'email@email.com', '000000', 'sd9fasdfasd9fasd89fsad9f8', 1, 1, '2000-01-01', '1');

CREATE TABLE orgaos_tipos (
    orgao_tipo_id varchar(36) NOT NULL,
    orgao_tipo_nome varchar(255) NOT NULL UNIQUE,
    orgao_tipo_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    orgao_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    orgao_tipo_criado_por varchar(36) NOT NULL,
    orgao_tipo_gabinete varchar(36) NOT NULL,
    orgao_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (orgao_tipo_id),
    CONSTRAINT fk_orgao_tipo_criado_por FOREIGN KEY (orgao_tipo_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_orgao_tipo_gabinete FOREIGN KEY (orgao_tipo_gabinete) REFERENCES gabinete(gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE orgaos (
    orgao_id varchar(36) NOT NULL,
    orgao_nome text NOT NULL,
    orgao_email varchar(255) NOT NULL UNIQUE,
    orgao_telefone varchar(255) DEFAULT NULL,
    orgao_endereco text,
    orgao_bairro text,
    orgao_municipio varchar(255) NOT NULL,
    orgao_estado varchar(255) NOT NULL,
    orgao_cep varchar(255) DEFAULT NULL,
    orgao_tipo varchar(36) NOT NULL,
    orgao_informacoes text,
    orgao_site text,
    orgao_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    orgao_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    orgao_criado_por varchar(36) NOT NULL,
    orgao_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (orgao_id),
    CONSTRAINT fk_orgao_criado_por FOREIGN KEY (orgao_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_orgao_tipo FOREIGN KEY (orgao_tipo) REFERENCES orgaos_tipos(orgao_tipo_id),
    CONSTRAINT fk_orgao_gabinete FOREIGN KEY (orgao_gabinete) REFERENCES gabinete(gabinete_id)

) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (1, 'Tipo não informado', 'Sem tipo definido', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (2, 'Ministério', 'Órgão responsável por uma área específica do governo federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (3, 'Autarquia Federal', 'Órgão com autonomia administrativa e financeira', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (4, 'Empresa Pública Federal', 'Órgão que realiza atividades econômicas como públicos, correios, eletrobras..', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (5, 'Universidade Federal', 'Instituição de ensino superior federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (6, 'Polícia Federal', 'Órgão responsável pela segurança e investigação em âmbito federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (7, 'Governo Estadual', 'Órgão executivo estadual responsável pela administração de um estado', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (8, 'Assembleia Legislativa Estadual', 'Órgão legislativo estadual responsável pela criação de leis estaduais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (9, 'Prefeitura', 'Órgão executivo municipal responsável pela administração local', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (10, 'Câmara Municipal', 'Órgão legislativo municipal responsável pela criação de leis municipais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (11, 'Entidade Civil', 'Organização sem fins lucrativos que atua em prol de causas sociais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (12, 'Escola estadual', 'Escolas estaduais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (13, 'Escola municipal', 'Escolas municipais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (14, 'Escola Federal', 'Escolas federais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (15, 'Partido Político', 'Partido Político', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (16, 'Câmara Federal', 'Câmara Federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (17, 'Senado Federal', 'Senado Federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (18, 'Presidência da Repúlica', 'Presidência da Repúlica', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (19, 'Veículo de comunicação', 'Jornais, revistas, sites de notícias, emissoras de rádio e TV', 1, 1);



INSERT INTO orgaos (orgao_id, orgao_nome, orgao_email, orgao_municipio, orgao_estado, orgao_tipo, orgao_criado_por, orgao_gabinete) 
VALUES 
(1, 'Órgão não informado', 'email@email', 'municipio', 'estado', 1, 1, 1);


CREATE TABLE pessoas_tipos (
    pessoa_tipo_id varchar(36) NOT NULL,
    pessoa_tipo_nome varchar(255) NOT NULL UNIQUE,
    pessoa_tipo_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    pessoa_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pessoa_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    pessoa_tipo_criado_por varchar(36) NOT NULL,
    pessoa_tipo_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (pessoa_tipo_id),
    CONSTRAINT fk_pessoa_tipo_criado_por FOREIGN KEY (pessoa_tipo_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_pessoa_tipo_gabinete FOREIGN KEY (pessoa_tipo_gabinete) REFERENCES gabinete (gabinete_id)

) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (1, 'Sem tipo definido', 'Sem tipo definido', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (2, 'Familiares', 'Familiares do deputado', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (3, 'Empresários', 'Donos de empresa', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (4, 'Eleitores', 'Eleitores em geral', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (5, 'Imprensa', 'Jornalistas, diretores de jornais, assessoria', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (7, 'Amigos', 'Amigos pessoais do deputado', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (8, 'Prefeito(as)', 'Prefeitos(as) de municípios', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (9, 'Deputado(a) Federal', 'Deputados Federal.', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (10, 'Governador(a)', 'Governadores(as) de estado', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (11, 'Deputado(a) Estadual', 'Deputados Estadual.', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (12, 'Vereador(a)', 'Vereador.', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (13, 'Senador(a)', 'Senador.', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (14, 'Ministro(a)', 'Ministro(a).', 1, 1);


CREATE TABLE pessoas_profissoes (
    pessoas_profissoes_id varchar(36) NOT NULL,
    pessoas_profissoes_nome varchar(255) NOT NULL UNIQUE,
    pessoas_profissoes_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    pessoas_profissoes_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pessoas_profissoes_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    pessoas_profissoes_criado_por varchar(36) NOT NULL,
    pessoas_profissoes_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (pessoas_profissoes_id),
    CONSTRAINT fk_pessoas_profissoes_criado_por FOREIGN KEY (pessoas_profissoes_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_pessoa_profissao_gabinete FOREIGN KEY (pessoas_profissoes_gabinete) REFERENCES gabinete (gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO pessoas_profissoes (pessoas_profissoes_id, pessoas_profissoes_nome, pessoas_profissoes_descricao, pessoas_profissoes_criado_por, pessoas_profissoes_gabinete) 
VALUES 
  (1, 'Profissão não informada', 'Profissão não informada', 1, 1),
  (2, 'Médico(a)', 'Profissional responsável por diagnosticar e tratar doenças', 1, 1),
  (3, 'Engenheiro(a) de Software', 'Profissional especializado em desenvolvimento e manutenção de sistemas de software', 1, 1),
  (4, 'Advogado(a)', 'Profissional que oferece consultoria e representação legal', 1, 1),
  (5, 'Professor(a)', 'Profissional responsável por ministrar aulas e orientar estudantes', 1, 1),
  (6, 'Enfermeiro(a)', 'Profissional da saúde que cuida e monitoriza pacientes', 1, 1),
  (7, 'Arquiteto(a)', 'Profissional que projeta e planeja edifícios e espaços urbanos', 1, 1),
  (8, 'Contador(a)', 'Profissional que gerencia contas e prepara relatórios financeiros', 1, 1),
  (9, 'Designer Gráfico(a)', 'Profissional especializado em criação visual e design', 1, 1),
  (10, 'Jornalista', 'Profissional que coleta, escreve e distribui notícias', 1, 1),
  (11, 'Chef de Cozinha', 'Profissional que planeja, dirige e prepara refeições em restaurantes', 1, 1),
  (12, 'Psicólogo(a)', 'Profissional que realiza avaliações psicológicas e oferece terapia', 1, 1),
  (13, 'Fisioterapeuta', 'Profissional que ajuda na reabilitação física de pacientes', 1, 1),
  (14, 'Veterinário(a)', 'Profissional responsável pelo cuidado e tratamento de animais', 1, 1),
  (15, 'Fotógrafo(a)', 'Profissional que captura e edita imagens fotográficas', 1, 1),
  (16, 'Tradutor(a)', 'Profissional que converte textos de um idioma para outro', 1, 1),
  (17, 'Administrador(a)', 'Profissional que gerencia operações e processos em uma organização', 1, 1),
  (18, 'Biólogo(a)', 'Profissional que estuda organismos vivos e seus ecossistemas', 1, 1),
  (19, 'Economista', 'Profissional que analisa dados econômicos e desenvolve modelos de previsão', 1, 1),
  (20, 'Programador(a)', 'Profissional que escreve e testa códigos de software', 1, 1),
  (21, 'Cientista de Dados', 'Profissional que analisa e interpreta grandes volumes de dados', 1, 1),
  (22, 'Analista de Marketing', 'Profissional que desenvolve e implementa estratégias de marketing', 1, 1),
  (23, 'Engenheiro(a) Civil', 'Profissional que projeta e constrói infraestrutura como pontes e edifícios', 1, 1),
  (24, 'Cozinheiro(a)', 'Profissional que prepara e cozinha alimentos em ambientes como restaurantes', 1, 1),
  (25, 'Social Media', 'Profissional que gerencia e cria conteúdo para redes sociais', 1, 1),
  (26, 'Auditor(a)', 'Profissional que examina e avalia registros financeiros e operacionais', 1, 1),
  (27, 'Técnico(a) em Informática', 'Profissional que presta suporte técnico e manutenção de hardware e software', 1, 1),
  (28, 'Líder de Projeto', 'Profissional que coordena e supervisiona projetos para garantir a conclusão bem-sucedida', 1, 1),
  (29, 'Químico(a)', 'Profissional que realiza pesquisas e experimentos químicos', 1, 1),
  (30, 'Gerente de Recursos Humanos', 'Profissional responsável pela gestão de pessoal e políticas de recursos humanos', 1, 1),
  (31, 'Engenheiro(a) Eletricista', 'Profissional que projeta e implementa sistemas elétricos e eletrônicos', 1, 1),
  (32, 'Designer de Moda', 'Profissional que cria e desenvolve roupas e acessórios', 1, 1),
  (33, 'Engenheiro(a) Mecânico(a)', 'Profissional que projeta e desenvolve sistemas mecânicos e máquinas', 1, 1),
  (34, 'Web Designer', 'Profissional que cria e mantém layouts e interfaces de sites', 1, 1),
  (35, 'Geólogo(a)', 'Profissional que estuda a composição e estrutura da Terra', 1, 1),
  (36, 'Profissional de Segurança da Informação', 'Profissional que protege sistemas e dados contra ameaças e ataques', 1, 1),
  (37, 'Consultor(a) Financeiro(a)', 'Profissional que oferece orientação sobre gestão e planejamento financeiro', 1, 1),
  (38, 'Artista Plástico(a)', 'Profissional que cria obras de arte em diversos meios e materiais', 1, 1),
  (39, 'Profissional de Logística', 'Profissional que coordena e gerencia operações de logística e cadeia de suprimentos', 1, 1),
  (40, 'Fonoaudiólogo(a)', 'Profissional que avalia e trata problemas de comunicação e linguagem', 1, 1),
  (41, 'Corretor(a) de Imóveis', 'Profissional que facilita a compra, venda e aluguel de propriedades', 1, 1),
  (42, 'Bacharel em Direito', 'Pessoal formada em direito mas não exerce a advocacia', 1, 1),
  (76, 'Delegado(a)', 'Delegado de polícia civil ou federal', 1, 1),
  (77, 'Policial Civil', 'Delegado de polícia civil ou federal', 1, 1),
  (78, 'Policial Militar', 'Delegado de polícia civil ou federal', 1, 1),
  (79, 'Policial Penal', 'Delegado de polícia civil ou federal', 1, 1),
  (47, 'Bombeiro Militar', 'Profissional responsável por atividades de prevenção e combate a incêndios, salvamentos e atendimento pré-hospitalar', 1, 1),
  (48, 'Militar do Exército', 'Profissional das Forças Armadas que atua na defesa terrestre do país e em missões institucionais', 1, 1),
  (49, 'Militar da Marinha', 'Profissional das Forças Armadas que atua na defesa naval do país e em operações marítimas', 1, 1),
  (50, 'Militar da Aeronáutica', 'Profissional das Forças Armadas que atua na defesa aérea do país e em operações aeronáuticas', 1, 1),
  (51, 'Cientista da Computação', 'Profissional que estuda e desenvolve soluções computacionais para problemas complexos', 1, 1),
  (52, 'Engenheiro(a) de Dados', 'Profissional que projeta e mantém estruturas para coleta e análise de dados', 1, 1),
  (53, 'Especialista em UX/UI', 'Profissional que projeta experiências e interfaces digitais centradas no usuário', 1, 1),
  (54, 'Desenvolvedor(a) Mobile', 'Profissional especializado em criação de aplicativos para dispositivos móveis', 1, 1),
  (55, 'Especialista em Segurança Cibernética', 'Profissional que protege sistemas e redes contra ameaças digitais', 1, 1),
  (56, 'Administrador(a) de Banco de Dados', 'Profissional responsável pela organização, segurança e desempenho de bases de dados', 1, 1),
  (57, 'Pedagogo(a)', 'Profissional que atua na formação educacional e no desenvolvimento de métodos de ensino', 1, 1),
  (58, 'Historiador(a)', 'Profissional que pesquisa, interpreta e analisa fatos históricos', 1, 1),
  (59, 'Sociólogo(a)', 'Profissional que estuda o comportamento social e as relações humanas', 1, 1),
  (60, 'Antropólogo(a)', 'Profissional que estuda as culturas humanas e suas evoluções', 1, 1),
  (61, 'Bibliotecário(a)', 'Profissional responsável pela organização, conservação e acesso a acervos informacionais', 1, 1),
  (62, 'Técnico(a) de Enfermagem', 'Profissional da área da saúde que auxilia enfermeiros e médicos no atendimento a pacientes', 1, 1),
  (63, 'Nutricionista', 'Profissional que orienta sobre alimentação saudável e dietas personalizadas', 1, 1),
  (64, 'Terapeuta Ocupacional', 'Profissional que promove a reabilitação de pacientes por meio de atividades terapêuticas', 1, 1),
  (65, 'Cirurgião-Dentista', 'Profissional da saúde especializado no diagnóstico e tratamento odontológico', 1, 1),
  (66, 'Farmacêutico(a)', 'Profissional responsável pela manipulação, distribuição e orientação sobre medicamentos', 1, 1),
  (67, 'Promotor(a) de Justiça', 'Profissional do Ministério Público que atua na defesa da ordem jurídica e interesses sociais', 1, 1),
  (68, 'Juiz(a)', 'Profissional que exerce a magistratura e decide processos judiciais', 1, 1),
  (69, 'Defensor(a) Público(a)', 'Profissional que presta assistência jurídica gratuita à população', 1, 1),
  (70, 'Agente Penitenciário(a)', 'Profissional que atua na vigilância e segurança de presídios', 1, 1),
  (71, 'Gestor(a) de Projetos', 'Profissional responsável pelo planejamento, execução e controle de projetos', 1, 1),
  (72, 'Consultor(a) de RH', 'Profissional que atua na gestão estratégica de pessoas nas organizações', 1, 1),
  (73, 'Mentor(a) Profissional', 'Profissional que orienta o desenvolvimento de carreira e habilidades', 1, 1),
  (74, 'Especialista em ESG', 'Profissional que desenvolve e aplica práticas ambientais, sociais e de governança', 1, 1),
  (75, 'Produtor(a) Cultural', 'Profissional que organiza e gerencia eventos e projetos culturais', 1, 1);


CREATE TABLE pessoas (
    pessoa_id varchar(36) NOT NULL,
    pessoa_nome varchar(255) NOT NULL,
    pessoa_aniversario DATE DEFAULT NULL,
    pessoa_email varchar(255) NOT NULL UNIQUE,
    pessoa_telefone varchar(255) DEFAULT NULL,
    pessoa_endereco text DEFAULT NULL,
    pessoa_bairro text,
    pessoa_municipio varchar(255) DEFAULT NULL,
    pessoa_estado varchar(255) DEFAULT NULL,
    pessoa_cep varchar(255) DEFAULT NULL,
    pessoa_sexo varchar(255) DEFAULT NULL,
    pessoa_facebook varchar(255) DEFAULT NULL,
    pessoa_instagram varchar(255) DEFAULT NULL,
    pessoa_x varchar(255) DEFAULT NULL,
    pessoa_informacoes text DEFAULT NULL,
    pessoa_profissao varchar(36) NOT NULL,
    pessoa_partido varchar(255) DEFAULT 'S.PART.',
    pessoa_tipo varchar(36) NOT NULL,
    pessoa_orgao varchar(36) NOT NULL,
    pessoa_foto text DEFAULT NULL,
    pessoa_criada_em timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    pessoa_atualizada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    pessoa_criada_por varchar(36) NOT NULL,
    pessoa_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (pessoa_id),
    CONSTRAINT fk_pessoa_criada_por FOREIGN KEY (pessoa_criada_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_pessoa_tipo FOREIGN KEY (pessoa_tipo) REFERENCES pessoas_tipos(pessoa_tipo_id),
    CONSTRAINT fk_pessoa_profissao FOREIGN KEY (pessoa_profissao) REFERENCES pessoas_profissoes(pessoas_profissoes_id),
    CONSTRAINT fk_pessoa_orgao FOREIGN KEY (pessoa_orgao) REFERENCES orgaos(orgao_id),
    CONSTRAINT fk_pessoa_gabinete FOREIGN KEY (pessoa_gabinete) REFERENCES gabinete (gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;






CREATE TABLE documentos_tipos (
    documento_tipo_id varchar(36) NOT NULL,
    documento_tipo_nome varchar(255) NOT NULL UNIQUE,
    documento_tipo_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    documento_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    documento_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    documento_tipo_criado_por varchar(36) NOT NULL,
    documento_tipo_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (documento_tipo_id),
    CONSTRAINT fk_documento_tipo_criado_por FOREIGN KEY (documento_tipo_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_documento_tipo_gabinete FOREIGN KEY (documento_tipo_gabinete) REFERENCES gabinete (gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) VALUES (1, 'Sem tipo definido', 'Sem tipo definido', 1, 1);
INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) 
VALUES (2, 'Ofício', 'Documento utilizado para comunicações formais entre órgãos ou instituições', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) 
VALUES (3, 'Requerimento', 'Documento formal solicitando algo de uma instituição ou órgão', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) 
VALUES (4, 'Carta', 'Documento informal ou formal que transmite informações ou solicitações', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) 
VALUES (5, 'Memorando', 'Documento utilizado para comunicação interna entre setores de uma organização', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) 
VALUES (6, 'Ata', 'Documento que registra os acontecimentos e decisões de uma reunião ou evento', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) 
VALUES (7, 'Termo de Compromisso', 'Documento que formaliza um compromisso ou acordo entre as partes', 1, 1);


CREATE TABLE documentos(
    documento_id varchar(36) NOT NULL,
    documento_titulo VARCHAR(255) NOT NULL UNIQUE,
    documento_resumo text,
    documento_arquivo text,
    documento_ano int,
    documento_tipo varchar(36) NOT NULL,
    documento_orgao varchar(36) NOT NULL,
    documento_criado_por varchar(36) NOT NULL,
    documento_gabinete varchar(36) NOT NULL,
    documento_criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    documento_atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(documento_id),
    CONSTRAINT fk_documento_criado_por FOREIGN KEY (documento_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_documento_orgao FOREIGN KEY (documento_orgao) REFERENCES orgaos(orgao_id),
    CONSTRAINT fk_documento_tipo FOREIGN KEY (documento_tipo) REFERENCES documentos_tipos(documento_tipo_id),
    CONSTRAINT fk_documento_gabinete FOREIGN KEY (documento_gabinete) REFERENCES gabinete(gabinete_id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;




CREATE TABLE emendas_status (
    emendas_status_id varchar(36) NOT NULL,
    emendas_status_nome varchar(255) NOT NULL UNIQUE,
    emendas_status_descricao TEXT NOT NULL,
    emendas_status_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    emendas_status_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    emendas_status_criado_por varchar(36) NOT NULL,
    emendas_status_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (emendas_status_id),
    CONSTRAINT fk_emendas_status_criado_por FOREIGN KEY (emendas_status_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_emendas_status_gabinete FOREIGN KEY (emendas_status_gabinete) REFERENCES gabinete (gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO emendas_status (emendas_status_id, emendas_status_nome, emendas_status_descricao, emendas_status_criado_por, emendas_status_gabinete)
VALUES
    (1, 'Criada', 'A emenda foi criada no sistema.', '1', '1'),
    (2, 'Em Análise', 'A emenda foi recebida e está sendo analisada pelos responsáveis.', '1', '1'),
    (3, 'Aprovada', 'A emenda foi aprovada e aguarda os próximos trâmites.', '1', '1'),
    (4, 'Rejeitada', 'A emenda foi rejeitada por não atender aos critérios estabelecidos.', '1', '1'),
    (5, 'Em Execução', 'A emenda foi aprovada e está em fase de execução.', '1', '1'),
    (6, 'Paga', 'A emenda foi totalmente executada e finalizada.', '1', '1'),
    (7, 'Pendente de Documentação', 'A emenda aguarda a entrega de documentos para seguir para análise.', '1', '1'),
    (8, 'Cancelada', 'A emenda foi cancelada por solicitação do proponente.', '1', '1'),
    (9, 'Aguardando Liberação', 'A emenda foi aprovada e está aguardando a liberação de recursos.', '1', '1'),
    (10, 'Revisão Necessária', 'A emenda precisa de ajustes antes de seguir para aprovação.', '1', '1'),
    (11, 'Suspensa', 'A execução da emenda foi temporariamente suspensa.', '1', '1');


CREATE TABLE emendas_objetivos (
    emendas_objetivos_id varchar(36) NOT NULL,
    emendas_objetivos_nome varchar(255) NOT NULL UNIQUE,
    emendas_objetivos_descricao TEXT NOT NULL,
    emendas_objetivos_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    emendas_objetivos_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    emendas_objetivos_criado_por varchar(36) NOT NULL,
    emendas_objetivos_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (emendas_objetivos_id),
    CONSTRAINT fk_emendas_objetivos_criado_por FOREIGN KEY (emendas_objetivos_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_emendas_objetivos_status_gabinete FOREIGN KEY (emendas_objetivos_gabinete) REFERENCES gabinete (gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO emendas_objetivos (emendas_objetivos_id, emendas_objetivos_nome, emendas_objetivos_descricao, emendas_objetivos_criado_por, emendas_objetivos_gabinete)
VALUES
    (1, 'Sem objetivo definido', 'Sem objetivo definido.', '1', '1'),
    (2, 'Transferência especial', 'Emenda PIX.', '1', '1'),
    (3, 'Saúde', 'Destinação de recursos para hospitais, unidades de saúde e aquisição de equipamentos médicos.', '1', '1'),
    (4, 'Educação', 'Investimentos em escolas, creches, universidades e formação de professores.', '1', '1'),
    (5, 'Infraestrutura', 'Obras de pavimentação, saneamento básico e construção de equipamentos públicos.', '1', '1'),
    (6, 'Segurança Pública', 'Apoio a projetos para melhoria das forças de segurança, aquisição de viaturas e equipamentos.', '1', '1'),
    (7, 'Cultura', 'Fomento a atividades culturais, reforma de teatros, bibliotecas e museus.', '1', '1'),
    (8, 'Esporte', 'Incentivo ao esporte e lazer, construção de quadras e centros esportivos.', '1', '1'),
    (9, 'Assistência Social', 'Apoio a programas sociais voltados para populações vulneráveis.', '1', '1'),
    (10, 'Agricultura', 'Fomento à agricultura familiar, assistência técnica e compra de equipamentos.', '1', '1'),
    (11, 'Meio Ambiente', 'Projetos de sustentabilidade, preservação ambiental e energias renováveis.', '1', '1'),
    (12, 'Turismo', 'Apoio a iniciativas de turismo sustentável e infraestrutura turística.', '1', '1'),
    (13, 'Ciência e Tecnologia', 'Fomento à inovação, pesquisa e desenvolvimento tecnológico.', '1', '1'),
    (14, 'Transporte', 'Melhoria da mobilidade urbana e transporte público.', '1', '1'),
    (15, 'Habitação', 'Investimentos em programas habitacionais e urbanização de áreas carentes.', '1', '1');


CREATE TABLE emendas (
    emenda_id varchar(36) NOT NULL,
    emenda_numero INT NOT NULL,
    emenda_ano INT NOT NULL,
    emenda_valor DECIMAL(12,2),
    emenda_descricao TEXT NOT NULL,
    emenda_status VARCHAR(36) NOT NULL,
    emenda_orgao VARCHAR(36) NOT NULL,
    emenda_municipio VARCHAR(50) NOT NULL,
    emenda_estado VARCHAR(3) NOT NULL,
    emenda_objetivo VARCHAR(36) NOT NULL,
    emenda_informacoes TEXT NULL,
    emenda_tipo VARCHAR(12) NOT NULL,
    emenda_gabinete VARCHAR(36) NOT NULL,
    emenda_criado_por VARCHAR(36) NOT NULL,
    emenda_criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    emenda_atualizada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (emenda_id),
    CONSTRAINT fk_emenda_status FOREIGN KEY (emenda_status) REFERENCES emendas_status (emendas_status_id),
    CONSTRAINT fk_emendas_objetivos FOREIGN KEY (emenda_objetivo) REFERENCES emendas_objetivos (emendas_objetivos_id),
    CONSTRAINT fk_emenda_criado_por FOREIGN KEY (emenda_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_emenda_gabinete FOREIGN KEY (emenda_gabinete) REFERENCES gabinete (gabinete_id),
    CONSTRAINT fk_emenda_orgao FOREIGN KEY (emenda_orgao) REFERENCES orgaos (orgao_id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


CREATE TABLE postagem_status(
    postagem_status_id varchar(36) NOT NULL,
    postagem_status_nome VARCHAR(255) NOT NULL UNIQUE,
    postagem_status_descricao TEXT NULL,
    postagem_status_criado_por varchar(36) NOT NULL,
    postagem_status_gabinete varchar(36) NOT NULL,
    postagem_status_criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    postagem_status_atualizada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(postagem_status_id),
    CONSTRAINT fk_postagem_status_criado_por FOREIGN KEY (postagem_status_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_postagem_status_gabinete FOREIGN KEY (postagem_status_gabinete) REFERENCES gabinete(gabinete_id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_gabinete) VALUES (1, 'Iniciada', 'Iniciada uma postagem', 1, 1);
INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_gabinete) VALUES (2, 'Em produção', 'Postagem em fase de produção', 1,1);
INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_gabinete) VALUES (3, 'Em aprovação', 'Postagem em fase de aprovação', 1,1);
INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_gabinete) VALUES (4, 'Aprovada', 'Postagem aprovada', 1,1);
INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_gabinete) VALUES (5, 'Postada', 'Postagem postada', 1,1);

CREATE TABLE postagens(
    postagem_id varchar(36) NOT NULL,
    postagem_titulo VARCHAR(255) NOT NULL UNIQUE,
    postagem_data VARCHAR(255),
    postagem_pasta TEXT, 
    postagem_informacoes TEXT,
    postagem_midias TEXT,  
    postagem_status varchar(36) NOT NULL,
    postagem_criada_por varchar(36) NOT NULL,
    postagem_gabinete varchar(36) NOT NULL,
    postagem_criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    postagem_atualizada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(postagem_id),
    CONSTRAINT fk_postagem_criada_por FOREIGN KEY (postagem_criada_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_postagem_status FOREIGN KEY (postagem_status) REFERENCES postagem_status(postagem_status_id),
    CONSTRAINT fk_postagem_gabinete FOREIGN KEY (postagem_gabinete) REFERENCES gabinete(gabinete_id)

)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


CREATE TABLE clipping_tipos (
    clipping_tipo_id varchar(36) NOT NULL,
    clipping_tipo_nome varchar(255) NOT NULL UNIQUE,
    clipping_tipo_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    clipping_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    clipping_tipo_criado_por varchar(36) NOT NULL,
    clipping_tipo_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (clipping_tipo_id),
    CONSTRAINT fk_clipping_tipo_criado_por FOREIGN KEY (clipping_tipo_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_clipping_tipo_gabinete FOREIGN KEY (clipping_tipo_gabinete) REFERENCES gabinete(gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO clipping_tipos (clipping_tipo_id, clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_gabinete) VALUES (1, 'Sem tipo definido', 'Sem tipo definido', 1,1);
INSERT INTO clipping_tipos (clipping_tipo_id, clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_gabinete) VALUES (2, 'Notícia Jornalística', 'Matéria Jornalistica de site, revista, blog...', 1, 1);
INSERT INTO clipping_tipos (clipping_tipo_id, clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_gabinete) VALUES (3, 'Post de rede social', 'Post de instagram, facebook....', 1, 1);


CREATE TABLE clipping (
    clipping_id varchar(36) NOT NULL,
    clipping_resumo TEXT NOT NULL,
    clipping_titulo TEXT NOT NULL,
    clipping_link VARCHAR(255) NOT NULL UNIQUE,
    clipping_orgao varchar(36) NOT NULL,
    clipping_arquivo VARCHAR(255),
    clipping_data date NOT NULL,
    clipping_tipo varchar(36) NOT NULL,
    clipping_criado_por varchar(36) NOT NULL,
    clipping_gabinete varchar(36) NOT NULL,
    clipping_criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    clipping_atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (clipping_id),
    CONSTRAINT fk_clipping_criado_por FOREIGN KEY (clipping_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_clipping_orgao FOREIGN KEY (clipping_orgao) REFERENCES orgaos(orgao_id),
    CONSTRAINT fk_clipping_tipo FOREIGN KEY (clipping_tipo) REFERENCES clipping_tipos(clipping_tipo_id),
    CONSTRAINT fk_clipping_gabinete FOREIGN KEY (clipping_gabinete) REFERENCES gabinete(gabinete_id)

)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


CREATE TABLE agenda_tipo (
    agenda_tipo_id varchar(36) NOT NULL,
    agenda_tipo_nome varchar(255) NOT NULL UNIQUE,
    agenda_tipo_descricao TEXT NOT NULL,
    agenda_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    agenda_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    agenda_tipo_criado_por varchar(36) NOT NULL,
    agenda_tipo_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (agenda_tipo_id),
    CONSTRAINT fk_agenda_tipo_criado_por FOREIGN KEY (agenda_tipo_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_agenda_tipo_status_gabinete FOREIGN KEY (agenda_tipo_gabinete) REFERENCES gabinete (gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO agenda_tipo (agenda_tipo_id, agenda_tipo_nome, agenda_tipo_descricao, agenda_tipo_criado_por, agenda_tipo_gabinete)
VALUES (1, 'Agenda parlamentar', 'Agenda legislativa do deputado.', '1', '1'),(2, 'Agenda partidária', 'Agenda relacionada ao partido.', '1', '1'), (3, 'Agenda pessoal', 'Agenda pessoal do parlamentar.', '1', '1');


CREATE TABLE agenda_situacao (
    agenda_situacao_id varchar(36) NOT NULL,
    agenda_situacao_nome varchar(255) NOT NULL UNIQUE,
    agenda_situacao_descricao TEXT NOT NULL,
    agenda_situacao_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    agenda_situacao_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    agenda_situacao_criado_por varchar(36) NOT NULL,
    agenda_situacao_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (agenda_situacao_id),
    CONSTRAINT fk_agenda_situacao_criado_por FOREIGN KEY (agenda_situacao_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_agenda_situacao_status_gabinete FOREIGN KEY (agenda_situacao_gabinete) REFERENCES gabinete (gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO agenda_situacao (agenda_situacao_id, agenda_situacao_nome, agenda_situacao_descricao, agenda_situacao_criado_por, agenda_situacao_gabinete)
VALUES 
(1, 'Situação não informada', 'Situação não informada.', 1, 1),
(2, 'Agendada', 'O compromisso foi marcado, mas ainda não ocorreu.', 1, 1),
(3, 'Finalizada', 'O compromisso ou tarefa foi completado ou realizado com sucesso.', 1, 1),
(4, 'Cancelada', 'O compromisso foi desmarcado ou cancelado, por algum motivo, e não será mais realizado.', 1, 1),
(5, 'Pendente', 'O compromisso ou tarefa foi adiado ou está esperando algum tipo de ação ou decisão antes de ser realizado.', 1, 1),
(6, 'Em Andamento', 'O compromisso ou tarefa está em execução ou já começou, mas ainda não foi concluído.', 1, 1),
(7, 'Remarcada', 'O compromisso foi reagendado para outro dia e hora, após uma alteração ou conflito de agenda.', 1, 1),
(8, 'Atrasada', 'O compromisso não foi cumprido no horário previsto e está atrasado.', 1, 1),
(9, 'Confirmada', 'O compromisso foi confirmado por todas as partes envolvidas, garantindo que ocorrerá como planejado.', 1, 1);


CREATE TABLE agenda(
    agenda_id varchar(36) NOT NULL,
    agenda_titulo VARCHAR(255) NOT NULL UNIQUE,
    agenda_situacao VARCHAR(255) NOT NULL,
    agenda_tipo VARCHAR(255) NOT NULL,
    agenda_data TIMESTAMP NOT NULL,
    agenda_local TEXT NOT NULL,
    agenda_estado VARCHAR(2) NOT NULL,
    agenda_informacoes TEXT DEFAULT NULL,
    agenda_criada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    agenda_atualizada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    agenda_criada_por varchar(36) NOT NULL,
    agenda_gabinete varchar(36) NOT NULL,
    PRIMARY KEY (agenda_id),
    CONSTRAINT fk_agenda_tipo FOREIGN KEY (agenda_tipo) REFERENCES agenda_tipo(agenda_tipo_id),
    CONSTRAINT fk_agenda_situacao FOREIGN KEY (agenda_situacao) REFERENCES agenda_situacao(agenda_situacao_id),
    CONSTRAINT fk_agenda_criada_por FOREIGN KEY (agenda_criada_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_agenda_gabinete FOREIGN KEY (agenda_gabinete) REFERENCES gabinete(gabinete_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;




  

CREATE TABLE proposicao_tema (
    proposicao_tema_id varchar(36) NOT NULL,
    proposicao_tema_nome varchar(255) NOT NULL UNIQUE,
    proposicao_tema_criado_por varchar(36) NOT NULL,
    proposicao_tema_gabinete varchar(36) NOT NULL,
    proposicao_tema_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    proposicao_tema_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (proposicao_tema_id),
    CONSTRAINT fk_proposicao_tema_criado_por FOREIGN KEY (proposicao_tema_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_proposicao_tema_gabinete FOREIGN KEY (proposicao_tema_gabinete) REFERENCES gabinete(gabinete_id)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci;


  INSERT INTO proposicao_tema (proposicao_tema_id, proposicao_tema_nome, proposicao_tema_criado_por, proposicao_tema_gabinete)
VALUES 
('1', 'Reforma Tributária', '1', '1'),
('2', 'Direitos Humanos', '1', '1'),
('3', 'Educação Pública', '1', '1'),
('4', 'Meio Ambiente', '1', '1'),
('5', 'Saúde Pública', '1', '1'),
('6', 'Tecnologia e Inovação', '1', '1'),
('7', 'Segurança Pública', '1', '1'),
('8', 'Infraestrutura e Transportes', '1', '1'),
('9', 'Reforma Trabalhista', '1', '1'),
('10', 'Cultura e Lazer', '1', '1'),
('11', 'Política Externa', '1', '1'),
('12', 'Justiça e Direito', '1', '1'),
('13', 'Agronegócio', '1', '1'),
('14', 'Emprego e Renda', '1', '1'),
('15', 'Igualdade de Gênero', '1', '1'),
('16', 'Impostos e Taxas', '1', '1'),
('17', 'Combate à Corrupção', '1', '1'),
('18', 'Reforma da Previdência', '1', '1'),
('19', 'Assistência Social', '1', '1'),
('20', 'Proteção aos Animais', '1', '1'),
('21', 'Sem tema definido', '1', '1');


CREATE TABLE nota_tecnica(
    nota_id varchar(36) NOT NULL,
    nota_proposicao BIGINT NOT NULL UNIQUE,
    nota_proposicao_apelido TEXT NULL,
    nota_proposicao_resumo TEXT NULL,
    nota_proposicao_tema varchar(36) NOT NULL,
    nota_texto TEXT NULL,
    nota_criada_por varchar(36) NOT NULL,
    nota_gabinete varchar(36) NOT NULL,
    nota_criada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    nota_atualizada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (nota_id),
    CONSTRAINT fk_nota_proposicao_tema FOREIGN KEY (nota_proposicao_tema) REFERENCES proposicao_tema(proposicao_tema_id),
    CONSTRAINT fk_nota_criada_por FOREIGN KEY (nota_criada_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_nota_nota_gabinete FOREIGN KEY (nota_gabinete) REFERENCES gabinete(gabinete_id)
)ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_general_ci;