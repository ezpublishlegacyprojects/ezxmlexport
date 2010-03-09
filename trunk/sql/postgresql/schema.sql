CREATE SEQUENCE ezxport_customers_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE SEQUENCE ezxport_exports_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE SEQUENCE ezxport_process_logs_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE TABLE ezxport_available_cclass_attr (
  contentclass_attribute_id INT   NOT NULL,
  contentclass_id           INT   NOT NULL,
    UNIQUE (contentclass_attribute_id ));

CREATE TABLE ezxport_available_cclasses (
  contentclass_id INT   NOT NULL,
    UNIQUE (contentclass_id ));

CREATE TABLE ezxport_customers (
  ID           integer DEFAULT nextval('ezxport_customers_s'::text) NOT NULL,
  NAME         VARCHAR(200)   NOT NULL,
  ftp_target   TEXT   NOT NULL,
  slicing_mode CHAR(1) DEFAULT '1' NOT NULL,
  CONSTRAINT pk_ezxport_customers PRIMARY KEY ( id ));

CREATE TABLE ezxport_exports (
  ID                      integer DEFAULT nextval('ezxport_exports_s'::text) NOT NULL,
  customer_id             INT   NOT NULL,
  NAME                    VARCHAR(200)   NOT NULL,
  description             VARCHAR(200)   NOT NULL,
  sources                 TEXT   NOT NULL,
  ftp_target              VARCHAR(200)   NOT NULL,
  slicing_mode            CHAR(1) DEFAULT '1' NOT NULL,
  start_date              VARCHAR(15)   DEFAULT '0'   NOT NULL,
  end_date                VARCHAR(15)   DEFAULT '0'   NOT NULL,
  export_schedule         VARCHAR(100)   NOT NULL,
  export_limit            INT   NOT NULL,
  export_from_last        SMALLINT   DEFAULT '0'   NOT NULL,
  compression             SMALLINT   NOT NULL,
  related_object_handling SMALLINT   NOT NULL,
  xslt_file               VARCHAR(70)   NOT NULL,
  export_hidden_nodes     SMALLINT   DEFAULT '0'   NOT NULL,
  CONSTRAINT pk_ezxport_exports PRIMARY KEY ( id ));

CREATE TABLE ezxport_process_logs (
  ID                   integer DEFAULT nextval('ezxport_process_logs_s'::text)   NOT NULL,
  export_id            INT   NOT NULL,
  start_date           VARCHAR(10)   NOT NULL,
  end_date             VARCHAR(10)   NOT NULL,
  start_transfert_date VARCHAR(10)   NOT NULL,
  end_transfert_date   VARCHAR(10)   NOT NULL,
  status               INT   NOT NULL,
  CONSTRAINT pk_ezxport_process_logs PRIMARY KEY ( id ));

CREATE TABLE ezxport_export_object_log (
  process_log_id   INT   NOT NULL,
  contentobject_id INT   NOT NULL,
  CONSTRAINT "FK_process_log_id" FOREIGN KEY ( process_log_id ) REFERENCES ezxport_process_logs(id) ON DELETE CASCADE);

SELECT setval('ezxport_customers_s', max(id)) , 'ezxport_customers' as tablename FROM ezxport_customers;
SELECT setval('ezxport_exports_s', max(id)) , 'ezxport_exports' as tablename FROM ezxport_exports;
SELECT setval('ezxport_process_logs_s', max(id)) , 'ezxport_export_object_log' as tablename FROM ezxport_process_logs;
