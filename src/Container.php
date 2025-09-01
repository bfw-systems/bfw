<?php

namespace BFW;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Exception;

/**
 * PSR-11 Container implementation for BFW framework
 * Manages AppSystems and other services for dependency injection
 */
class Container implements ContainerInterface
{
    /**
     * @const ERR_SERVICE_NOT_FOUND Exception code when service is not found
     */
    const ERR_SERVICE_NOT_FOUND = 1101005;
    
    /**
     * @const ERR_CONTAINER_EXCEPTION Exception code for container errors
     */
    const ERR_CONTAINER_EXCEPTION = 1101006;
    
    /**
     * @var array $services Registered services
     */
    protected $services = [];
    
    /**
     * @var array $instances Service instances cache
     */
    protected $instances = [];
    
    /**
     * {@inheritdoc}
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new ContainerNotFoundException(
                'Service "' . $id . '" not found in container.',
                self::ERR_SERVICE_NOT_FOUND
            );
        }
        
        // Return cached instance if already instantiated
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        
        $service = $this->services[$id];
        
        // If it's a callable, invoke it
        if (is_callable($service)) {
            try {
                $this->instances[$id] = $service($this);
                return $this->instances[$id];
            } catch (Exception $e) {
                throw new ContainerException(
                    'Error creating service "' . $id . '": ' . $e->getMessage(),
                    self::ERR_CONTAINER_EXCEPTION,
                    $e
                );
            }
        }
        
        // If it's an AppSystem instance, call its __invoke method
        if ($service instanceof Core\AppSystems\SystemInterface) {
            try {
                $this->instances[$id] = $service();
                return $this->instances[$id];
            } catch (Exception $e) {
                throw new ContainerException(
                    'Error invoking AppSystem "' . $id . '": ' . $e->getMessage(),
                    self::ERR_CONTAINER_EXCEPTION,
                    $e
                );
            }
        }
        
        // Return the service directly if it's not callable
        $this->instances[$id] = $service;
        return $this->instances[$id];
    }
    
    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
    
    /**
     * Register a service in the container
     * 
     * @param string $id Service identifier
     * @param mixed $service Service instance, callable, or AppSystem
     * 
     * @return self
     */
    public function set(string $id, $service): self
    {
        $this->services[$id] = $service;
        
        // Clear cached instance if it exists
        if (isset($this->instances[$id])) {
            unset($this->instances[$id]);
        }
        
        return $this;
    }
    
    /**
     * Remove a service from the container
     * 
     * @param string $id Service identifier
     * 
     * @return self
     */
    public function remove(string $id): self
    {
        unset($this->services[$id], $this->instances[$id]);
        return $this;
    }
    
    /**
     * Get all registered service identifiers
     * 
     * @return array
     */
    public function getServiceIds(): array
    {
        return array_keys($this->services);
    }
}

/**
 * Container exception when service is not found
 */
class ContainerNotFoundException extends Exception implements NotFoundExceptionInterface
{
}

/**
 * General container exception
 */
class ContainerException extends Exception implements ContainerExceptionInterface
{
}