--
-- PostgreSQL database dump
--

\restrict Fu5ASyyXBS2B9UBoLtQNH4UbK2ZKNWXVEpLvulrTnQxoQbW9Y2ibnbA8xjcwdER

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

-- Started on 2026-09-25 21:05:14

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 223 (class 1259 OID 17083)
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 17094)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 17183)
-- Name: ciudades; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ciudades (
    id bigint NOT NULL,
    pais_id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    latitud numeric(10,7),
    longitud numeric(10,7),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ciudades OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 17182)
-- Name: ciudades_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ciudades_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ciudades_id_seq OWNER TO postgres;

--
-- TOC entry 5119 (class 0 OID 0)
-- Dependencies: 234
-- Name: ciudades_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ciudades_id_seq OWNED BY public.ciudades.id;


--
-- TOC entry 229 (class 1259 OID 17136)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 17135)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- TOC entry 5120 (class 0 OID 0)
-- Dependencies: 228
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- TOC entry 237 (class 1259 OID 17198)
-- Name: historial; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.historial (
    id bigint NOT NULL,
    usuario_id bigint NOT NULL,
    ciudad_id bigint NOT NULL,
    presupuesto_cop numeric(15,2) NOT NULL,
    clima character varying(255),
    tasa numeric(15,4) NOT NULL,
    valor_convertido numeric(15,2) NOT NULL,
    fecha timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.historial OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 17197)
-- Name: historial_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.historial_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.historial_id_seq OWNER TO postgres;

--
-- TOC entry 5121 (class 0 OID 0)
-- Dependencies: 236
-- Name: historial_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.historial_id_seq OWNED BY public.historial.id;


--
-- TOC entry 227 (class 1259 OID 17121)
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 17106)
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 17105)
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- TOC entry 5122 (class 0 OID 0)
-- Dependencies: 225
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- TOC entry 220 (class 1259 OID 17057)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 17056)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 5123 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 231 (class 1259 OID 17156)
-- Name: monedas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.monedas (
    id bigint NOT NULL,
    codigo character varying(5) NOT NULL,
    nombre character varying(255) NOT NULL,
    simbolo character varying(10) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.monedas OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 17155)
-- Name: monedas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.monedas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.monedas_id_seq OWNER TO postgres;

--
-- TOC entry 5124 (class 0 OID 0)
-- Dependencies: 230
-- Name: monedas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.monedas_id_seq OWNED BY public.monedas.id;


--
-- TOC entry 233 (class 1259 OID 17167)
-- Name: paises; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.paises (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    codigo character varying(5) NOT NULL,
    moneda_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.paises OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 17166)
-- Name: paises_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.paises_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.paises_id_seq OWNER TO postgres;

--
-- TOC entry 5125 (class 0 OID 0)
-- Dependencies: 232
-- Name: paises_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.paises_id_seq OWNED BY public.paises.id;


--
-- TOC entry 222 (class 1259 OID 17067)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    correo character varying(255) NOT NULL,
    password_hash character varying(255) NOT NULL,
    idioma character varying(255) DEFAULT 'es'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 17066)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 5126 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 4911 (class 2604 OID 17186)
-- Name: ciudades id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ciudades ALTER COLUMN id SET DEFAULT nextval('public.ciudades_id_seq'::regclass);


--
-- TOC entry 4907 (class 2604 OID 17139)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 4912 (class 2604 OID 17201)
-- Name: historial id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial ALTER COLUMN id SET DEFAULT nextval('public.historial_id_seq'::regclass);


--
-- TOC entry 4906 (class 2604 OID 17109)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- TOC entry 4903 (class 2604 OID 17060)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 4909 (class 2604 OID 17159)
-- Name: monedas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monedas ALTER COLUMN id SET DEFAULT nextval('public.monedas_id_seq'::regclass);


--
-- TOC entry 4910 (class 2604 OID 17170)
-- Name: paises id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paises ALTER COLUMN id SET DEFAULT nextval('public.paises_id_seq'::regclass);


--
-- TOC entry 4904 (class 2604 OID 17070)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 5099 (class 0 OID 17083)
-- Dependencies: 223
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- TOC entry 5100 (class 0 OID 17094)
-- Dependencies: 224
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- TOC entry 5111 (class 0 OID 17183)
-- Dependencies: 235
-- Data for Name: ciudades; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ciudades (id, pais_id, nombre, latitud, longitud, created_at, updated_at) FROM stdin;
1	1	Londres	51.5074000	-0.1278000	\N	\N
2	1	Mánchester	53.4808000	-2.2426000	\N	\N
3	2	Tokio	35.6762000	139.6503000	\N	\N
4	2	Osaka	34.6937000	135.5023000	\N	\N
5	3	Nueva Delhi	28.6139000	77.2090000	\N	\N
6	3	Bombay	19.0760000	72.8777000	\N	\N
7	4	Copenhague	55.6761000	12.5683000	\N	\N
8	4	Aarhus	56.1629000	10.2039000	\N	\N
\.


--
-- TOC entry 5105 (class 0 OID 17136)
-- Dependencies: 229
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 5113 (class 0 OID 17198)
-- Dependencies: 237
-- Data for Name: historial; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.historial (id, usuario_id, ciudad_id, presupuesto_cop, clima, tasa, valor_convertido, fecha, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5103 (class 0 OID 17121)
-- Dependencies: 227
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- TOC entry 5102 (class 0 OID 17106)
-- Dependencies: 226
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- TOC entry 5096 (class 0 OID 17057)
-- Dependencies: 220
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_09_25_221226_create_monedas_table	1
5	2026_09_25_221247_create_pais_table	1
6	2026_09_25_221303_create_ciudads_table	1
7	2026_09_25_221321_create_historials_table	1
\.


--
-- TOC entry 5107 (class 0 OID 17156)
-- Dependencies: 231
-- Data for Name: monedas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.monedas (id, codigo, nombre, simbolo, created_at, updated_at) FROM stdin;
1	GBP	Libra esterlina	£	\N	\N
2	JPY	Yen	¥	\N	\N
3	INR	Rupia india	₹	\N	\N
4	DKK	Corona danesa	kr	\N	\N
\.


--
-- TOC entry 5109 (class 0 OID 17167)
-- Dependencies: 233
-- Data for Name: paises; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.paises (id, nombre, codigo, moneda_id, created_at, updated_at) FROM stdin;
1	Inglaterra	GBP	1	\N	\N
2	Japon	JPY	2	\N	\N
3	India	INR	3	\N	\N
4	Dinamarca	DKK	4	\N	\N
\.


--
-- TOC entry 5098 (class 0 OID 17067)
-- Dependencies: 222
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, nombre, correo, password_hash, idioma, created_at, updated_at) FROM stdin;
1	Test usuario	test@viajes.com	$2y$12$5g26yTKrQgfA6Xjb0u9kGO6TtGfNmCZQ5gIgfVoUc1nosAaTbIJ9i	es	\N	\N
\.


--
-- TOC entry 5127 (class 0 OID 0)
-- Dependencies: 234
-- Name: ciudades_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.ciudades_id_seq', 8, true);


--
-- TOC entry 5128 (class 0 OID 0)
-- Dependencies: 228
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- TOC entry 5129 (class 0 OID 0)
-- Dependencies: 236
-- Name: historial_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.historial_id_seq', 1, false);


--
-- TOC entry 5130 (class 0 OID 0)
-- Dependencies: 225
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- TOC entry 5131 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 7, true);


--
-- TOC entry 5132 (class 0 OID 0)
-- Dependencies: 230
-- Name: monedas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.monedas_id_seq', 4, true);


--
-- TOC entry 5133 (class 0 OID 0)
-- Dependencies: 232
-- Name: paises_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.paises_id_seq', 4, true);


--
-- TOC entry 5134 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- TOC entry 4925 (class 2606 OID 17103)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 4922 (class 2606 OID 17092)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 4941 (class 2606 OID 17191)
-- Name: ciudades ciudades_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ciudades
    ADD CONSTRAINT ciudades_pkey PRIMARY KEY (id);


--
-- TOC entry 4933 (class 2606 OID 17151)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 4935 (class 2606 OID 17154)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 4943 (class 2606 OID 17211)
-- Name: historial historial_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial
    ADD CONSTRAINT historial_pkey PRIMARY KEY (id);


--
-- TOC entry 4930 (class 2606 OID 17134)
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- TOC entry 4927 (class 2606 OID 17119)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 4915 (class 2606 OID 17065)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 4937 (class 2606 OID 17165)
-- Name: monedas monedas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monedas
    ADD CONSTRAINT monedas_pkey PRIMARY KEY (id);


--
-- TOC entry 4939 (class 2606 OID 17176)
-- Name: paises paises_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paises
    ADD CONSTRAINT paises_pkey PRIMARY KEY (id);


--
-- TOC entry 4917 (class 2606 OID 17082)
-- Name: users users_correo_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_correo_unique UNIQUE (correo);


--
-- TOC entry 4919 (class 2606 OID 17080)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 4920 (class 1259 OID 17093)
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- TOC entry 4923 (class 1259 OID 17104)
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- TOC entry 4931 (class 1259 OID 17152)
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- TOC entry 4928 (class 1259 OID 17120)
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- TOC entry 4945 (class 2606 OID 17192)
-- Name: ciudades ciudades_pais_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ciudades
    ADD CONSTRAINT ciudades_pais_id_foreign FOREIGN KEY (pais_id) REFERENCES public.paises(id) ON DELETE CASCADE;


--
-- TOC entry 4946 (class 2606 OID 17217)
-- Name: historial historial_ciudad_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial
    ADD CONSTRAINT historial_ciudad_id_foreign FOREIGN KEY (ciudad_id) REFERENCES public.ciudades(id) ON DELETE CASCADE;


--
-- TOC entry 4947 (class 2606 OID 17212)
-- Name: historial historial_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial
    ADD CONSTRAINT historial_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 4944 (class 2606 OID 17177)
-- Name: paises paises_moneda_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paises
    ADD CONSTRAINT paises_moneda_id_foreign FOREIGN KEY (moneda_id) REFERENCES public.monedas(id) ON DELETE CASCADE;


-- Completed on 2026-09-25 21:05:14

--
-- PostgreSQL database dump complete
--

\unrestrict Fu5ASyyXBS2B9UBoLtQNH4UbK2ZKNWXVEpLvulrTnQxoQbW9Y2ibnbA8xjcwdER

