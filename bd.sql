drop table if exists depart cascade;

create table depart (
    dept_no numeric(2)  constraint pk_depart primary key,
    dnombre varchar(20) not null,
    loc     varchar(50)
);
